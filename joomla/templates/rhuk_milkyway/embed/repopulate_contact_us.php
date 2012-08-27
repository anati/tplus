<?php
$values = $_GET;
if( ! empty($values)){
    ?>
    <script type="text/javascript">
    (function(){
    var $input = "";
    <?php
    foreach($values as $field => $value)
    {
        $field = str_replace("'", "\\\\'", $field);
        $value= str_replace("'", "\\\\'", $value);
        ?>
        $input = jQuery("form [name=<?php echo $field; ?>]");
        if( $input ){
            var $type = $input.attr('type');
            if( $type == 'checkbox'){
                $input = jQuery("form input[name=<?php echo $field; ?>][value=<?php echo $value; ?>]");
                $input.attr('checked', true);
            } else {
                $input.val("<?php echo $value; ?>");
            }
        }
    <?php
    }
    ?>
    })();
    </script>
<?php
}
