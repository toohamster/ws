<?php
use Ws\Mvc\Request;

$sg = Request::get('sg', 500);
?>
<script src="//cdn.bootcss.com/jquery/1.11.2/jquery.js"></script>
<script>
    var ids = {};
    var calc_num = 0;
    var ii = 0;

    function setContent(json) {
        console.log(++ii);
        if (!json.id) return;
        var id = json.id;
        if (ids[id]) return;

        ids[id] = 1;
        calc_num++;

        $('#v-asdebug').prepend('<p>[' + calc_num + '] ' + json.create_at + '<br>' + json.content + '</p><hr>');

        if (calc_num == 500) {
            calc_num = 0;
            ids = {};
        }

    }

    function refresh() {
        $.ajax('<?php echo $url?>', {
            type: 'GET',
            dataType: 'json',
            data: {f: 'json', '<?php echo $qargs['qauth']?>': '<?php echo $qargs['authval']?>', '<?php echo $qargs['qtag']?>': '<?php echo $qargs['tagval']?>'},
            async: false,
            cache: false,
            error: function (xhr, status, et) {
                console.warn(status, et);
            },
            complete: function (xhr, status, et) {
                var sg = <?php echo (int) $sg;?>;
                sg = sg || 500;
                if (sg < 20) sg = 20;
                setTimeout('refresh()', sg);
            },
            success: function (json, status, xhr) {
                setContent(json);
            }
        });
    }

    $(document).ready(function () {
        refresh();
    });
</script>

<div>
    <div class="main-content-title">
        <div class="float-left">
            <h2 class="">AsDebug 调试工具</h2>
        </div>
    </div>
    <div class="main-content-body" id="v-asdebug">
    </div>
</div>