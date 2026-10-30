<?php
require_once __DIR__ . '/config/auth.php';
requireArea('dashboard');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Cylinder Maintenance Workshop - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/includes/navbar.php'; ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Workshop Dashboardd</h2>
        
        <?php
        require_once 'config/database.php';
        $conn = getDB();
        
        $totalCustomers = $conn->query("SELECT COUNT(*) as c FROM customers")->fetch_assoc()['c'];
        $totalVehicles = $conn->query("SELECT COUNT(*) as c FROM vehicles")->fetch_assoc()['c'];
        $pendingJobs = $conn->query("SELECT COUNT(*) as c FROM work_orders WHERE Status IN ('Pending','In Progress')")->fetch_assoc()['c'];
        $completedToday = $conn->query("SELECT COUNT(*) as c FROM work_orders WHERE DATE(DateCompleted) = CURDATE()")->fetch_assoc()['c'];
        $lowStock = $conn->query("SELECT COUNT(*) as c FROM parts WHERE Quantity <= ReorderLevel")->fetch_assoc()['c'];
        $unpaidInvoices = $conn->query("SELECT COUNT(*) as c FROM invoices WHERE Status != 'Paid'")->fetch_assoc()['c'];
        $revenueToday = $conn->query("SELECT COALESCE(SUM(TotalAmount),0) as r FROM invoices WHERE DATE(InvoiceDate)=CURDATE() AND Status='Paid'")->fetch_assoc()['r'];
        $revenueMonth = $conn->query("SELECT COALESCE(SUM(TotalAmount),0) as r FROM invoices WHERE MONTH(InvoiceDate)=MONTH(CURDATE()) AND YEAR(InvoiceDate)=YEAR(CURDATE()) AND Status='Paid'")->fetch_assoc()['r'];
        ?>
        
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Customers</h6>
                                <h3><?php echo $totalCustomers; ?></h3>
                            </div>
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Vehicles</h6>
                                <h3><?php echo $totalVehicles; ?></h3>
                            </div>
                            <i class="bi bi-car-front fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Active Jobs</h6>
                                <h3><?php echo $pendingJobs; ?></h3>
                            </div>
                            <i class="bi bi-clipboard-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Completed Today</h6>
                                <h3><?php echo $completedToday; ?></h3>
                            </div>
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Low Stock Items</h6>
                                <h3><?php echo $lowStock; ?></h3>
                            </div>
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Unpaid Invoices</h6>
                                <h3><?php echo $unpaidInvoices; ?></h3>
                            </div>
                            <i class="bi bi-cash-stack fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Revenue Today</h6>
                                <h4><?php echo formatCurrency($revenueToday); ?> Birr</h4>
                            </div>
                            <i class="bi bi-currency-dollar fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Revenue This Month</h6>
                                <h4><?php echo formatCurrency($revenueMonth); ?> Birr</h4>
                            </div>
                            <i class="bi bi-graph-up-arrow fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-task"></i> Recent Job Cards</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>WO No.</th>
                                        <th>Vehicle</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT wo.WorkOrderID, wo.WorkOrderNo, wo.Status, wo.DateReceived,
                                                   v.PlateNumber, v.Make, v.Model,
                                                   c.FirstName, c.LastName
                                            FROM work_orders wo
                                            JOIN vehicles v ON wo.VehicleID = v.VehicleID
                                            JOIN customers c ON wo.CustomerID = c.CustomerID
                                            ORDER BY wo.DateReceived DESC LIMIT 10";
                                    $result = $conn->query($sql);
                                    while($row = $result->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><strong><?php echo h($row['WorkOrderNo']); ?></strong></td>
                                        <td><?php echo h($row['PlateNumber']); ?> - <?php echo h($row['Make'].' '.$row['Model']); ?></td>
                                        <td><?php echo h($row['FirstName'].' '.$row['LastName']); ?></td>
                                        <td><span class="badge bg-<?php 
                                            echo $row['Status']=='Completed'?'success':($row['Status']=='In Progress'?'warning':($row['Status']=='Pending'?'info':'secondary'));
                                        ?>"><?php echo h($row['Status']); ?></span></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['DateReceived'])); ?></td>
                                        <td><a href="pages/jobcard.php?id=<?php echo $row['WorkOrderID']; ?>" class="btn btn-sm btn-primary">View</a></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Low Stock Alert</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $low = $conn->query("SELECT PartName, Quantity, ReorderLevel, PartCode FROM parts WHERE Quantity <= ReorderLevel ORDER BY Quantity ASC LIMIT 10");
                            if($low->num_rows == 0):
                            ?>
                            <li class="list-group-item text-success">All items well stocked</li>
                            <?php else:
                            while($p = $low->fetch_assoc()):
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo h($p['PartName']); ?></strong><br>
                                    <small class="text-muted"><?php echo h($p['PartCode']); ?></small>
                                </div>
                                <span class="badge bg-danger"><?php echo $p['Quantity']; ?>/<?php echo $p['ReorderLevel']; ?></span>
                            </li>
                            <?php endwhile; endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        <small>&copy; <?php echo date('Y'); ?> Car Cylinder Maintenance Workshop App developed by tariku</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
