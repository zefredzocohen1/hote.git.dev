<nav class="navbar navbar-default navbar-fixed-top" id="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><?php NAME_WEBSITE?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li id="language">
                        <div class="dropdown dropdown-flat selected ready">
                          <button class="dropdown-toggle form-control form-control-icon icon-language" type="button" data-toggle="dropdown"><span class="display">Tiếng Việt</span></button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="language" style="margin-right: -80px;">
                              <li role="presentation"><a role="menuitem" tabindex="-1" href="#" data-value="eng">English</a></li>
                              <li role="presentation"><a role="menuitem" tabindex="-1" href="#" data-value="vie" class="active">Tiếng Việt</a></li>
                          </ul>
                          <div class="dropdown-menu-chevron" style="left: 57px;"></div>
                          <input type="hidden" name="language" value="vie">
                        </div>
                    </li>
                <?php if(isset($_SESSION['user_name'])&&$_SESSION['role_id']==3){?>
                    <li><a href="<?php echo base_url()?>">Xin chào <?php echo $_SESSION['user_name']?></a></li>
                <?php }else{?>
                    <li><a href="<?php echo base_url().SiteDefault?>register">Đăng kí</a></li>
                    <li><a href="<?php echo base_url().SiteDefault?>login">Đăng nhập</a></li>
                <?php }?>
                    <li>
                        <ul>
                            <a href="<?php echo base_url().SiteDefault?>login">Ngôn ngữ</a>
                        </ul>
                    </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>
