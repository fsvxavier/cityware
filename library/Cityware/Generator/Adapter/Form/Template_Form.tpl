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
            <!--<div class="table-responsive"> -->
            <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                <?php echo $this->form($this->formview, null); ?>
            </div>
            <!--</div>-->
        </div>
    </div>
</div>