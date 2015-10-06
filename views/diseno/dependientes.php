<?php
    $this->title = 'Diseño-Dependientes';
?>
<div class="row">
    <div class="col-xs-2">
        <?php echo $this->render('menus/depMenu',['menu'=>$menu]); ?>
    </div>
    <div class="col-xs-10">
        <?php
            if(isset($r)) {
                switch ($r) {
                    case 'list':
                        echo $this->render('tables/ordenesDep', ['orden' => $orden, 'search' => $search]);
                        echo $this->render('@app/views/share/scripts/modalView',['size'=>'large']);
                        break;
                }
            }
        ?>
    </div>
</div>