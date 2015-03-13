<div class="container-fluid">
    <div class="row">
        <?php
        if ($this->popup != 'true') {
            echo $this->render('%moduleNameLower%/menu.phtml');
        }
        ?>
        <div class="main-container">
            <div class="page-header container-back-button">
                <div class="display-table-cell">
                    <h1><?php echo $this->translate('title_action'); ?></h1>
                    <h4><?php echo $this->translate('subtitle_action'); ?></h4>
                </div>
                <div class="display-table-cell-middle">
                    <a href="<?php echo $this->linkController; ?>" class="btn btn-warning pull-right"><i class="fa fa-angle-double-left"></i> Voltar</a>
                </div>
            </div>
            <?php echo $this->render('%moduleNameLower%/menugridlisttrash.phtml'); ?>
            <!--<div class="table-responsive"> -->
            <?php echo $this->datagrid; ?>
            <!--</div>-->
        </div>
    </div>
</div>