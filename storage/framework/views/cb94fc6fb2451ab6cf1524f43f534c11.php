

<?php $__env->startSection('content'); ?>
<?php
    // If request is AJAX (modal load), render only the partial content so it can be injected into modal
    if(request()->ajax()) {
        echo view('stocks._ledger_content', compact('product','entries','locationId'))->render();
        return; // stop further rendering
    }

    // Otherwise render full page and include the partial
?>

<?php echo $__env->make('stocks._ledger_content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Stock Ledger'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/stocks/ledger.blade.php ENDPATH**/ ?>