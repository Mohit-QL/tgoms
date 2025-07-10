<div class="pagination" style="text-align: right;">
    <!-- First and Previous Buttons -->
    <?php if ($currentPage > 1): ?>
        <input type='submit' name='firstResults' value='First'>
        <input type='submit' name='previousResults' value='Previous'>
    <?php endif; ?>

    <!-- Ellipsis before the range if needed -->
    <?php if ($startPage > 1): ?>
        <span style='color: black;font-size:20px;'>...</span>
    <?php endif; ?>

    <!-- Page Number Buttons -->
    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
        <?php if ($i == $currentPage): ?>
            <strong style='padding: 4px 8px;background-color: black;color: white;'><?php echo $i; ?></strong>
        <?php else: ?>
            <input type='submit' name='page<?php echo $i; ?>' value='<?php echo $i; ?>' style='margin:0 2px;'>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- Ellipsis after the range if needed -->
    <?php if ($endPage < $totalPages): ?>
        <span style='color: black;font-size:20px;'>...</span>
    <?php endif; ?>

    <!-- Next and Last Buttons -->
    <?php if ($currentPage < $totalPages): ?>
        <input type='submit' name='nextResults' value='Next'>
        <input type='submit' name='lastResults' value='Last'>
    <?php endif; ?>
</div>