<?php

?>
<div class="categories well">
    <h2>Licence packages</h2>
    <form action="<?php echo JRoute::_('index.php?option=com_catalogue&view=aggregion') ?>"
          method="post" id="adminForm" name="adminForm" class="form-validate">
        <table class="packages-table table table-borderless">
            <thead>
            <th>Name</th>
            <th>Store view</th>
            </thead>
            <tbody>
                <?php foreach($packages as $package): ?>
                    <?php if($package['status'] != 'sale') continue; ?>
                    <tr>
                        <td>
                            <p><?php echo $package['name'] ?></p>
                        </td>
                        <td>
                            <select class="form-control" name="<?php echo $package['id']; ?>">
                                <option value="0">Undefined</option>
                                <?php foreach($memberships as $membership): ?>
                                    <option value="<?php echo $membership->membership_id; ?>"
                                        <?php if($membership->membership_id == $package['membership_id'])
                                            echo 'selected="selected"';
                                        ?>
                                        >
                                        <?php echo $membership->getTranslation('ru')->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value="fields"/>
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>