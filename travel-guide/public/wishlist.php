<?php foreach($items as $item): ?>

<div id="item_<?php echo $item['id']; ?>">

    <h3><?php echo $item['title']; ?></h3>
    <p><?php echo $item['country']; ?> | <?php echo $item['cost_level']; ?></p>

    <!-- IMPORTANT: wishlist id পাঠাও -->
    <button onclick="removeWishlist(<?php echo $item['id']; ?>)">
        Remove
    </button>

</div>

<?php endforeach; ?>