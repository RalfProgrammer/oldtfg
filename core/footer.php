<?php
    if($go_to){?>
        <script type="text/javascript">
            $(function(){
                _Navigator.go("<?= $go_to?>");
            })
        </script><?php
    }?>
    </body>
</html>
