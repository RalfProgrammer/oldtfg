
<div class="hv-summary_patient row">
    <input type="hidden" name="summary_type" value="patient">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12 col-md-6"><?php
                include $CONFIG->dir . 'services/chat/view/widget_messages.php'; ?>
            </div>
            <div class="col-xs-12 col-md-6"><?php
                include $CONFIG->dir . 'services/calendar/view/widget_calendar.php'; ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12 col-md-6"><?php
                include $CONFIG->dir . 'services/medicine/view/widget_medicines.php'; ?>
            </div>
            <div class="col-xs-12 col-md-6"><?php
                include $CONFIG->dir . 'services/users/view/widget_notes.php'; ?>
            </div>
        </div>
    </div>
</div>