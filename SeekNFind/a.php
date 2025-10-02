<div id="pending">
            <?php if ($pending_result && mysqli_num_rows($pending_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($pending_result)): ?>
                        <!-- Pending Items -->
                        <form action="admin_actions.php" method="post">
                                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success">Approve</button>
                                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                </form>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No pending items found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="tracking">
            <?php if ($approved_result && mysqli_num_rows($approved_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($approved_result)): ?>
              <!-- Approved Items -->
                <?php endwhile; ?>
            <?php else: ?>
                <p>No tracking items found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="completed" class="tabcontent">
<!-- Complete Items -->
                <?php endwhile; ?>
            <?php else: ?>
                <p>No completed items found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>