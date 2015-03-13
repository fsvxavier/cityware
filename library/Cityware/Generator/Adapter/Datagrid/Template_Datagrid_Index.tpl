<div class="container-fluid">
    <div class="row">
        <?php
        if ($this->popup != 'true') {
            echo $this->render('%moduleNameLower%/menu.phtml');
        }
        ?>
        <div class="main-container">
            <div class="page-header">
                <h1><?php echo $this->translate('title_action'); ?></h1>
                <h4><?php echo $this->translate('subtitle_action'); ?></h4>
            </div>
            <?php echo $this->render('%moduleNameLower%/menugridlist.phtml'); ?>
            <!--<div class="table-responsive"> -->
            <?php echo $this->datagrid; ?>
            <!--</div>-->
        </div>
    </div>
</div>