<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center my-2">
            <h2><i class="bi bi-bar-chart-fill"></i> Profit & Loss</h2>
            <a href="<?php echo base_url('admin/reports/accounts/profit-loss-sold-value'); ?>" class="btn btn-info">Profit on Sold Items</a>
            <a href="<?php echo base_url('admin/reports/accounts'); ?>" class="btn btn-primary">Accounts</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form method="get" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label>From</label>
                        <input type="date" name="from" value="<?= $from ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>To</label>
                        <input type="date" name="to" value="<?= $to ?>" class="form-control">
                    </div>
                    <div class="col-md-4 align-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <a href="<?= base_url('admin/reports/accounts/profit-loss') ?>" class="btn btn-danger btn-sm">Reset</a>
                        -
                        <a target="_blank" href="<?= base_url("admin/reports/accounts/export_profit_loss/pdf?from=$from&to=$to") ?>" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> Export PDF</a>
                        <a target="_blank" href="<?= base_url("admin/reports/accounts/export_profit_loss/excel?from=$from&to=$to") ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Export Excel</a>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th>Total Purchase</th>
                                    <td class="text-danger text-end">₹<?= number_format($total_purchase, 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Other Expenses</th>
                                    <td class="text-danger text-end">₹<?= number_format($total_expense, 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Current Stock Valuation</th>
                                    <td class="text-success text-end">₹<?= number_format($current_stock_value, 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Total Invoices</th>
                                    <td class="text-success text-end">₹<?= number_format($total_invoice, 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Other Income</th>
                                    <td class="text-success text-end">₹<?= number_format($total_income, 2) ?></td>
                                </tr>
                                <tr class="fw-bold">
                                    <th>Net Profit / Loss</th>
                                    <td class="text-end <?= $net_profit >= 0 ? 'text-success' : 'text-danger' ?>">
                                        ₹<?= number_format($net_profit, 2) ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="plChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>

<script>
    const plChart = new Chart(document.getElementById('plChart'), {
        type: 'bar',
        data: {
            labels: ['Invoices', 'Income', 'Stock Value', 'Purchase', 'Expense'],
            datasets: [{
                label: 'Amount (₹)',
                data: [
                    <?= $total_invoice ?>,
                    <?= $total_income ?>,
                    <?= $current_stock_value ?>,
                    <?= $total_purchase ?>,
                    <?= $total_expense ?>
                ],
                backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6c757d'],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>