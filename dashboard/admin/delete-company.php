<?php
// Move up two levels to find the database connection
include '../../includes/db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Start a transaction for safety
        $pdo->beginTransaction();

        /** * FIX FOR ERROR #1701: 
         * Temporarily disable foreign key checks so we can delete a company 
         * even if it is referenced in the 'jobs' table.
         */
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
        $stmt->execute([$id]);

        // Re-enable safety checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        $pdo->commit();
        
        // Redirect back to the management list with a success status
        header("Location: manage-companies.php?deleted=success");
        exit();

    } catch (PDOException $e) {
        // Rollback changes if something goes wrong
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Error deleting company: " . $e->getMessage());
    }
} else {
    // If no ID is provided, just go back
    header("Location: manage-companies.php");
    exit();
}