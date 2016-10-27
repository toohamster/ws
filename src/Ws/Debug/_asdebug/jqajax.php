<?php
use Ws\Mvc\Request;

$qi = $qargs['q'];
$qtag = $qargs['qtag'];
$ai = Request::get($qi);
$atag = Request::get($qtag);
?>
<script type="text/javascript">
    if (window.jQuery) {
        $.ajaxSetup({
            data: {
                '<?php echo $qi?>': '<?php echo $ai?>',
                '<?php echo $qtag?>': '<?php echo $atag;?>'
            }
        });
        window.asdebug = function (url) {
            var ss = '<?php echo $qi?>=<?php echo $ai?>&<?php echo $qtag?>=<?php echo $atag;?>';
            if ('' != url && 'javascript:;' != url && '#' != url) {
                url = url.replace(ss, '');
                var pieces = url.split("?");
                url = pieces[0] + '?' + ss;
                if (pieces.length > 1) {
                    url += '&' + pieces[1];
                }
            }
            return url;
        };
        $(document).ready(function () {
            $('a').each(function () {
                $(this).attr('href', window.asdebug($(this).attr('href')));
            });
            var frmHidden = '<input type="hidden" name="<?php echo $qi?>" value="<?php echo $ai?>">';
            frmHidden += '<input type="hidden" name="<?php echo $qtag?>" value="<?php echo $atag;?>">';
            $('form').each(function () {
                $(this).append(frmHidden);
            });
        });
    }
</script>