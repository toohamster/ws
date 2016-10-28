<?php namespace Ws\Plugins\Hadoop;

use Exception;
use Ws\Plugins\Hadoop\Tools\Curl as HadoopCurl;

class WebHDFS
{
    private $host;
    private $port;
    private $user;
    private $namenode_rpc_host;
    private $namenode_rpc_port;
    private $debug;

    /**
     * @var \Ws\Plugins\Hadoop\Tools\Curl
     */
    private $curl;

    public function __construct(
        $host,
        $port,
        $user = '',
        $namenodeRpcHost = '',
        $namenodeRpcPort = '',
        $debug = false
    )
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->namenode_rpc_host = $namenodeRpcHost;
        $this->namenode_rpc_port = $namenodeRpcPort;
        $this->debug = $debug;
        $this->curl = new HadoopCurl($this->debug);
    }

    // File and Directory Operations

    public function create($path, $filename)
    {
        if (!file_exists($filename)) {
            return false;
        }

        $url = $this->_buildUrl($path, ['op' => 'CREATE']);
        $redirectUrl = $this->curl->putLocation($url);

        $result = $this->curl->putFile($redirectUrl, $filename);
        if ($result !== true) {
            throw $this->getResponseErrorException($this->curl->getLastRequestContentResult());
        }
        return $result;
    }

    public function createWithData($path, $data)
    {
        $url = $this->_buildUrl($path, ['op' => 'CREATE']);
        $redirectUrl = $this->curl->putLocation($url);
        $result = false;
        if ($redirectUrl) {
            $result = $this->curl->putData($redirectUrl, $data);
        }
        if ($result !== true) {
            throw $this->getResponseErrorException($this->curl->getLastRequestContentResult());
        }
        return $result;
    }

    public function append($path, $string, $bufferSize = '')
    {
        $url = $this->_buildUrl($path, ['op' => 'APPEND', 'buffersize' => $bufferSize]);
        $redirectUrl = $this->curl->postLocation($url);
        return $this->curl->postString($redirectUrl, $string);
    }

    public function concat($path, $sources)
    {
        $url = $this->_buildUrl($path, ['op' => 'CONCAT', 'sources' => $sources]);
        return $this->curl->post($url);
    }

    public function open($path, $offset = '', $length = '', $bufferSize = '')
    {
        $url = $this->_buildUrl($path, ['op' => 'OPEN', 'offset' => $offset, 'length' => $length, 'buffersize' => $bufferSize]);
        $result = $this->curl->getWithRedirect($url);
        if ($this->curl->validateLastRequest()) {
            return $result;
        }
        throw $this->getResponseErrorException($this->curl->getLastRequestContentResult());
    }

    public function mkdirs($path, $permission = '')
    {
        $url = $this->_buildUrl($path, ['op' => 'MKDIRS', 'permission' => $permission]);
        return $this->curl->put($url);
    }

    public function createSymLink($path, $destination, $createParent = '')
    {
        $url = $this->_buildUrl($destination, ['op' => 'CREATESYMLINK', 'destination' => $path, 'createParent' => $createParent]);
        return $this->curl->put($url);
    }

    public function rename($path, $destination)
    {
        $url = $this->_buildUrl($path, ['op' => 'RENAME', 'destination' => $destination]);
        return $this->curl->put($url);
    }

    public function delete($path, $recursive = '')
    {
        $url = $this->_buildUrl($path, ['op' => 'DELETE', 'recursive' => $recursive]);
        return $this->curl->delete($url);
    }

    public function getFileStatus($path)
    {
        $url = $this->_buildUrl($path, ['op' => 'GETFILESTATUS']);
        return $this->curl->get($url);
    }

    public function listStatus($path)
    {
        $url = $this->_buildUrl($path, ['op' => 'LISTSTATUS']);
        if ($result = $this->curl->get($url)) {
            $result = json_decode($result);
            if (!is_null($result)) {
                return $result;
            } else {
                throw $this->getResponseErrorException($this->curl->getLastRequestContentResult());
            }
        } else {
            throw $this->getResponseErrorException($this->curl->getLastRequestContentResult());
        }
        return false;
    }

    public function listFiles($path, $recursive = false, $includeFileMetaData = false, $maxAmountOfFiles = false)
    {
        $result = [];
        $listStatusResult = $this->listStatus($path);
        if (isset($listStatusResult->FileStatuses->FileStatus)) {
            foreach ($listStatusResult->FileStatuses->FileStatus AS $fileEntity) {
                switch ($fileEntity->type) {
                    case 'DIRECTORY':
                        if ($recursive === true) {
                            $result = array_merge($result, $this->listFiles($path . $fileEntity->pathSuffix . '/', true, $includeFileMetaData, $maxAmountOfFiles - sizeof($result)));
                        }
                        break;
                    default:
                        if ($includeFileMetaData === true) {
                            $fileEntity->path = $path . $fileEntity->pathSuffix;
                            $result[] = $fileEntity;
                        } else {
                            $result[] = $path . $fileEntity->pathSuffix;
                        }
                }
                // recursion will be interrupted since we subtract the amount of the current result set from the maxAmountOfFiles amount with calling the next recursion
                if (sizeof($result) >= $maxAmountOfFiles) {
                    break;
                }
            }
        } else {
            throw $this->getResponseErrorException($this->curl->getLastRequestContentResult());
        }
        return $result;
    }

    public function listDirectories($path, $recursive = false, $includeFileMetaData = false)
    {
        $result = [];
        $listStatusResult = $this->listStatus($path);
        if (isset($listStatusResult->FileStatuses->FileStatus)) {
            foreach ($listStatusResult->FileStatuses->FileStatus AS $fileEntity) {
                switch ($fileEntity->type) {
                    case 'DIRECTORY':
                        if ($includeFileMetaData === true) {
                            $fileEntity->path = $path . $fileEntity->pathSuffix;
                            $result[] = $fileEntity;
                        } else {
                            $result[] = $path . $fileEntity->pathSuffix;
                        }
                        if ($recursive === true) {
                            $result = array_merge($result, $this->listDirectories($path . $fileEntity->pathSuffix . '/', $recursive, $includeFileMetaData));
                        }
                        break;
                }
            }
        } else {
            throw $this->getResponseErrorException($this->curl->getLastRequestContentResult());
        }
        return $result;
    }

    // Other File System Operations

    public function getContentSummary($path)
    {
        $result = false;
        $url = $this->_buildUrl($path, ['op' => 'GETCONTENTSUMMARY']);
        $rawResult = $this->curl->get($url);
        $resultDecoded = json_decode($rawResult);
        if (isset($resultDecoded->ContentSummary)) {
            $result = $resultDecoded->ContentSummary;
        } else {
            throw $this->getResponseErrorException($this->curl->getLastRequestContentResult());
        }
        return $result;
    }

    public function getFileChecksum($path)
    {
        $url = $this->_buildUrl($path, ['op' => 'GETFILECHECKSUM']);
        return $this->curl->getWithRedirect($url);
    }

    public function getHomeDirectory()
    {
        $url = $this->_buildUrl('', ['op' => 'GETHOMEDIRECTORY']);
        return $this->curl->get($url);
    }

    public function setPermission($path, $permission)
    {
        $url = $this->_buildUrl($path, ['op' => 'SETPERMISSION', 'permission' => $permission]);
        return $this->curl->put($url);
    }

    public function setOwner($path, $owner = '', $group = '')
    {
        $url = $this->_buildUrl($path, ['op' => 'SETOWNER', 'owner' => $owner, 'group' => $group]);
        return $this->curl->put($url);
    }

    public function setReplication($path, $replication)
    {
        $url = $this->_buildUrl($path, ['op' => 'SETREPLICATION', 'replication' => $replication]);
        return $this->curl->put($url);
    }

    public function setTimes($path, $modificationTime = '', $accessTime = '')
    {
        $url = $this->_buildUrl($path, ['op' => 'SETTIMES', 'modificationtime' => $modificationTime, 'accesstime' => $accessTime]);
        return $this->curl->put($url);
    }

    private function _buildUrl($path, $query_data)
    {
        if (strlen($path) && $path[0] == '/') {
            $path = substr($path, 1);
        }

        if (!isset($query_data['user.name'])) {
            $query_data['user.name'] = $this->user;
        }
        // it is required to specify the namenode rpc address in, at least, write requests
        if (!isset($query_data['namenoderpcaddress'])) {
            $query_data['namenoderpcaddress'] = $this->namenode_rpc_host . ':' . $this->namenode_rpc_port;
        }
        return 'http://' . $this->host . ':' . $this->port . '/webhdfs/v1/' . $path . '?' . http_build_query(array_filter($query_data));
    }

    /**
     * returns a generated exception for given response data
     *
     * @param $responseData
     * @return \Exception
     */
    private function getResponseErrorException($responseData)
    {
        $data = json_decode($responseData);

        $exceptionCode = 1;
        $exceptionMessage = 'invalid/unknown response/exception: ' . $responseData;
        if (!is_null($data)) {
            if (
                isset($data->RemoteException->exception) &&
                isset($data->RemoteException->javaClassName) &&
                isset($data->RemoteException->message)
            ) {
                $exceptionMessage = $data->RemoteException->exception . ' in ' . $data->RemoteException->javaClassName . "\n" . $data->RemoteException->message;
                switch ($data->RemoteException->javaClassName) {
                    case 'org.apache.hadoop.fs.FileAlreadyExistsException':
                        $exceptionCode = 2;
                        break;
                    case 'java.io.FileNotFoundException':
                        $exceptionCode = 3;
                        break;
                    case 'org.apache.hadoop.security.AccessControlException':
                        if (preg_match('/Permission denied/i', $data->RemoteException->message)) {
                            $exceptionCode = 4;
                        }
                        break;
                    default:
                        $exceptionCode = 5;
                        break;
                }
            }
        }

        throw new Exception($exceptionMessage, $exceptionCode);
    }
}
