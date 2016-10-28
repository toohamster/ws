<script type="text/javascript">
if (window.jQuery) {
    $.ajaxSetup({
        data: {
            '<?php echo $qargs['qauth']?>': '<?php echo $qargs['authval']?>',
            '<?php echo $qargs['qtag']?>': '<?php echo $qargs['tagval'];?>'
        }
    });
    window.asdebug = function (url) {
        var ss = '<?php echo $qargs['qauth']?>=<?php echo $qargs['authval']?>&<?php echo $qargs['qtag']?>=<?php echo $qargs['tagval'];?>';
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
        var frmHidden = '<input type="hidden" name="<?php echo $qargs['qauth']?>" value="<?php echo $qargs['authval']?>">';
        frmHidden += '<input type="hidden" name="<?php echo $qargs['qtag']?>" value="<?php echo $qargs['tagval'];?>">';
        $('form').each(function () {
            $(this).append(frmHidden);
        });
    });
}
</script>