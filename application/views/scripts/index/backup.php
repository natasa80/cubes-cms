
//<?php
//$this->headTitle('Clients');
//?>

<div id="content">
    <div class="container">

    </div>
</div>
<div class="wrapper" id="content-below">
    <div class="container">
        <div class="clients block">
            <h3 class="block-title">
                <span>Clients</span>
            </h3>
            <!--Recommended image sizing: 170px & 70px-->
            <ul class="thumbnails list-unstyled row">
                <?php
                foreach ($this->clients as $client) {
                    if ($client['status'] == Application_Model_DbTable_CmsMembers::STATUS_ENABLED) {
                        ?>
                        <li class="col-sm-2">
                            <a title="" data-original-title="<?php echo $this->escape($client['name']) ?>" data-placement="top" class="_tooltip" href="#">
                                <img alt="<?php echo $this->escape($client['name']) ?>" class="img-rounded" src="<?php echo $this->clientImgUrl($client); ?>">
                            </a>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</div>
