<nav class="navbar navbar-default" id="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo base_url('home');?>">STAR VIEW</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <?php if(isset($_SESSION['user_name'])&&$_SESSION['role_id']==3){?>
                <li><a href="<?php echo base_url()?>">Xin chào <?php echo $_SESSION['user_name']?></a></li>
            <?php }else{?>
                <li><a href="<?php echo base_url();?>home/register">Đăng kí</a></li>
                    <li><a href="<?php echo base_url();?>home/login">Đăng nhập</a></li>
                <?php }?>
                
            </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>