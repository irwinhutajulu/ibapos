<?php if(session('success') || session('error')): ?>
<script>
    (function(){
        try {
            <?php if(session('success')): ?>
            window.notify(<?php echo json_encode(session('success')); ?>, 'success');
            <?php endif; ?>
            <?php if(session('error')): ?>
            window.notify(<?php echo json_encode(session('error')); ?>, 'error');
            <?php endif; ?>
        } catch (e) {
            console.warn('notify helper not available', e);
        }
    })();
</script>
<?php endif; ?>
