<?php if (isset($component)) { $__componentOriginalcc3fab69f324b28db7adfa3ec8736b61 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc3fab69f324b28db7adfa3ec8736b61 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-form','data' => ['product' => $product,'categories' => $categories,'action' => route('products.update', $product),'method' => 'POST','mode' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($categories),'action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('products.update', $product)),'method' => 'POST','mode' => 'edit']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcc3fab69f324b28db7adfa3ec8736b61)): ?>
<?php $attributes = $__attributesOriginalcc3fab69f324b28db7adfa3ec8736b61; ?>
<?php unset($__attributesOriginalcc3fab69f324b28db7adfa3ec8736b61); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcc3fab69f324b28db7adfa3ec8736b61)): ?>
<?php $component = $__componentOriginalcc3fab69f324b28db7adfa3ec8736b61; ?>
<?php unset($__componentOriginalcc3fab69f324b28db7adfa3ec8736b61); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/products/partials/edit-form.blade.php ENDPATH**/ ?>