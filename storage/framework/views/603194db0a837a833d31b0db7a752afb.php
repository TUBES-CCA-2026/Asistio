<?php if($paginator->hasPages()): ?>
<nav role="navigation" aria-label="<?php echo e(__('Pagination Navigation')); ?>" class="pagination-nav">
    <ul class="pagination">

        
        <?php if($paginator->onFirstPage()): ?>
            <li class="pagination-item pagination-disabled" aria-disabled="true" aria-label="<?php echo e(__('pagination.previous')); ?>">
                <span class="pagination-link">&laquo; Previous</span>
            </li>
        <?php else: ?>
            <li class="pagination-item">
                <a class="pagination-link" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">&laquo; Previous</a>
            </li>
        <?php endif; ?>

        
        <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <?php if(is_string($element)): ?>
                <li class="pagination-item pagination-dots" aria-disabled="true"><span class="pagination-link"><?php echo e($element); ?></span></li>
            <?php endif; ?>

            
            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $paginator->currentPage()): ?>
                        <li class="pagination-item pagination-active" aria-current="page">
                            <span class="pagination-link"><?php echo e($page); ?></span>
                        </li>
                    <?php else: ?>
                        <li class="pagination-item">
                            <a class="pagination-link" href="<?php echo e($url); ?>" aria-label="<?php echo e(__('Go to page :page', ['page' => $page])); ?>"><?php echo e($page); ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($paginator->hasMorePages()): ?>
            <li class="pagination-item">
                <a class="pagination-link" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">Next &raquo;</a>
            </li>
        <?php else: ?>
            <li class="pagination-item pagination-disabled" aria-disabled="true" aria-label="<?php echo e(__('pagination.next')); ?>">
                <span class="pagination-link">Next &raquo;</span>
            </li>
        <?php endif; ?>

    </ul>
</nav>
<?php endif; ?><?php /**PATH C:\Users\ACER\Asistio\resources\views/vendor/pagination/asistio.blade.php ENDPATH**/ ?>