@extends('layouts.app')

@section('title', 'Welcome Back...!')

@section('content')
<div class="advanced-dashboard">
	<header class="analytics-heading">
		<div>
			<p class="analytics-eyebrow">SHOP RENTAL OVERVIEW</p>
			<h1>Analytics dashboard</h1>
			<p class="analytics-description">A live view of rental income and agreement health.</p>
		</div>
		<div class="analytics-date">
			<span class="analytics-date-label">REPORTING PERIOD</span>
			<strong>{{ \Carbon\Carbon::now()->format('F Y') }}</strong>
			<span>Updated {{ \Carbon\Carbon::now()->format('M j, Y') }}</span>
		</div>
	</header>

	<section class="analytics-grid" aria-label="Rental analytics">
		<a class="agreement-card-link" href="{{ route('dashboard.due-payments') }}" aria-label="View due payments">
		<article class="analytics-panel revenue-panel">
			<div class="analytics-panel-heading">
				<div>
					<p class="analytics-eyebrow">REVENUE PERFORMANCE</p>
					<h2>Monthly collections</h2>
					<p>Payments received over the last six months</p>
				</div>
				<span class="analytics-period-badge">Last 6 months</span>
			</div>
			<div class="revenue-chart-wrap">
				<svg id="revenue-chart" viewBox="0 0 640 270" role="img" aria-label="Monthly payment collections bar chart"></svg>
			</div>
		</article>
		</a>

		<a class="agreement-card-link" href="{{ route('dashboard.expired-agreements') }}" aria-label="View expiring and expired agreements">
		<article class="analytics-panel agreement-panel">
			<div class="analytics-panel-heading">
				<div>
					<p class="analytics-eyebrow">PORTFOLIO HEALTH</p>
					<h2>Agreement status</h2>
					<p>Shops grouped by agreement end date</p>
				</div>
			</div>
			<div class="agreement-chart-layout">
				<svg id="agreement-chart" viewBox="0 0 220 220" role="img" aria-label="Shop agreement status pie chart"></svg>
				<div id="agreement-legend" class="agreement-legend" aria-label="Agreement status totals"></div>
			</div>
		</article>
		</a>
	</section>

	<section class="collection-summary analytics-panel" aria-label="Current month rent collection">
		<div class="analytics-panel-heading">
			<div>
				<p class="analytics-eyebrow">MONTH-TO-DATE COLLECTION</p>
				<h2>Expected rent vs. collected</h2>
				<p>Collected payments compared with the combined monthly rent of all shops</p>
			</div>
			<span class="collection-performance-badge {{ $collectionSurplusThisMonth > 0 ? 'above-target' : ($totalDueThisMonth > 0 ? 'below-target' : '') }}">
				{{ $collectionSurplusThisMonth > 0 ? 'Above target' : ($totalDueThisMonth > 0 ? 'Collection in progress' : 'Target reached') }}
			</span>
		</div>
		<div class="collection-metrics">
			<div class="collection-metric">
				<span>Expected this month</span>
				<strong>Rs {{ number_format($totalExpectedIncome, 2) }}</strong>
			</div>
			<div class="collection-metric">
				<span>Collected this month</span>
				<strong class="collected-amount">Rs {{ number_format($totalPaidThisMonth, 2) }}</strong>
			</div>
			<div class="collection-metric collection-variance {{ $collectionSurplusThisMonth > 0 ? 'surplus' : '' }}">
				<span>{{ $collectionSurplusThisMonth > 0 ? 'Collected above expected' : 'Rent still outstanding' }}</span>
				<strong>Rs {{ number_format($collectionSurplusThisMonth > 0 ? $collectionSurplusThisMonth : $totalDueThisMonth, 2) }}</strong>
			</div>
		</div>
		<div class="collection-progress-track" role="progressbar"
			aria-label="Monthly rent collection progress"
			aria-valuenow="{{ min(100, round($collectionProgressPercent)) }}"
			aria-valuemin="0" aria-valuemax="100">
			<div class="collection-progress-fill {{ $collectionSurplusThisMonth > 0 ? 'surplus' : '' }}"
				style="width: {{ $collectionProgressPercent }}%"></div>
		</div>
		<div class="collection-progress-caption">
			<span>{{ number_format($collectionProgressPercent, 1) }}% of expected rent collected</span>
			@if($collectionSurplusThisMonth > 0)
				<span>Collections are Rs {{ number_format($collectionSurplusThisMonth, 2) }} above the expected total</span>
			@else
				<span>Remaining: Rs {{ number_format($totalDueThisMonth, 2) }}</span>
			@endif
		</div>
	</section>

	<!-- Advanced Widgets Grid -->
	<div class="dashboard-widgets-advanced">
		
		<!-- Row 1: Key Metrics -->
		<div class="widgets-row due-summary-row">
			<!-- Card 3: All Shops Due Amounts -->
			@unless (strtolower(auth()->user()->position ?? '') === 'accountant')
			<a href="{{ url('/reports/tenants') }}" class="card-link">
			@endunless
				<div class="widget-card-advanced defaulter-card">
					<div class="card-header-top">
						<div class="card-icon-circle">
							<i class="fas fa-exclamation-circle"></i>
						</div>
						<div class="card-title-section">
							<h3>All Shops Due</h3>
							<p class="card-subtitle">Outstanding amounts</p>
						</div>
					</div>
<div class="card-content-advanced defaulter-summary-card">
					<div class="defaulter-summary-grid">
						<div class="defaulter-summary-box due-payment-box">
							<p class="summary-title"><i class="fas fa-receipt"></i> Due Amount</p>
							<p class="summary-value">Rs {{ isset($totalDuePaymentAllShops) ? number_format($totalDuePaymentAllShops, 2) : '0.00' }}</p>
							<p class="summary-note">All shops combined</p>
						</div>
						<div class="defaulter-summary-box due-interest-box">
							<p class="summary-title"><i class="fas fa-percent"></i> Due Interest</p>
							<p class="summary-value">Rs {{ isset($totalDueInterestAllShops) ? number_format($totalDueInterestAllShops, 2) : '0.00' }}</p>
							<p class="summary-note">All shops combined</p>
						</div>
					</div>

					<div class="defaulter-total-box">
						<p class="summary-title"><i class="fas fa-calculator"></i> Total Due Balance</p>
						<p class="total-value">Rs {{ isset($totalDueBalanceAllShops) ? number_format($totalDueBalanceAllShops, 2) : '0.00' }}</p>
						<p class="summary-note">All shops combined</p>
					</div>
				</div>

						
					</div>
				</div>
			@unless (strtolower(auth()->user()->position ?? '') === 'accountant')
			</a>
			@endunless
		</div>

	</div>

	
</div>

<style>
	@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	.advanced-dashboard {
		background: linear-gradient(135deg, #f8f9fb 0%, #fff 100%);
		min-height: 100vh;
		padding: 30px 40px;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	}

	/* Premium Header */
	.dashboard-premium-header {
		background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
		border-radius: 20px;
		padding: 45px 50px;
		margin-bottom: 40px;
		box-shadow: 0 15px 45px rgba(118, 75, 162, 0.25);
		color: white;
		position: relative;
		overflow: hidden;
	}

	.dashboard-premium-header::before {
		content: '';
		position: absolute;
		top: -50%;
		right: -10%;
		width: 400px;
		height: 400px;
		background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
		border-radius: 50%;
		pointer-events: none;
	}

	.header-content-wrapper {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 40px;
		position: relative;
		z-index: 1;
	}

	.header-text h1.header-title {
		font-size: 32px;
		font-weight: 800;
		margin-bottom: 12px;
		display: flex;
		align-items: center;
		gap: 15px;
		letter-spacing: -0.5px;
	}

	.header-title i {
		font-size: 36px;
		opacity: 0.95;
	}

	.header-subtitle {
		font-size: 16px;
		opacity: 0.9;
		font-weight: 500;
	}

	.header-stats {
		display: flex;
		gap: 40px;
	}

	.quick-stat {
		display: flex;
		flex-direction: column;
		align-items: center;
		text-align: center;
		background: rgba(255, 255, 255, 0.15);
		padding: 20px 30px;
		border-radius: 15px;
		backdrop-filter: blur(10px);
		border: 1px solid rgba(255, 255, 255, 0.2);
		min-width: 120px;
	}

	.stat-value {
		font-size: 28px;
		font-weight: 800;
		display: block;
		margin-bottom: 6px;
	}

	.stat-label {
		font-size: 12px;
		opacity: 0.85;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	/* Advanced Widgets Grid */
	.dashboard-widgets-advanced {
		display: flex;
		flex-direction: column;
		gap: 30px;
	}

	.widgets-row {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		gap: 25px;
	}

	.card-link {
		text-decoration: none;
		color: inherit;
	}

	/* Advanced Card */
	.widget-card-advanced {
		background: white;
		border-radius: 18px;
		padding: 30px;
		box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
		transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
		border: 1px solid rgba(118, 75, 162, 0.1);
		position: relative;
		overflow: hidden;
		min-height: 450px;
	}

	.widget-card-advanced::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 5px;
		background: linear-gradient(90deg, #764ba2, #667eea);
	}

	.widget-card-advanced:hover {
		transform: translateY(-8px);
		box-shadow: 0 20px 50px rgba(118, 75, 162, 0.2);
		border-color: rgba(118, 75, 162, 0.25);
	}

	.card-header-top {
		display: flex;
		gap: 18px;
		margin-bottom: 25px;
		align-items: flex-start;
	}

	.card-icon-circle {
		width: 60px;
		height: 60px;
		border-radius: 14px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 28px;
		color: white;
		flex-shrink: 0;
		background: linear-gradient(135deg, #764ba2, #667eea);
		box-shadow: 0 8px 20px rgba(118, 75, 162, 0.3);
	}

	.card-title-section h3 {
		font-size: 18px;
		font-weight: 700;
		color: #1a1a2e;
		margin-bottom: 5px;
	}

	.card-subtitle {
		font-size: 13px;
		color: #999;
		font-weight: 500;
	}

	.card-content-advanced {
		position: relative;
	}

	.metric-value {
		font-size: 42px;
		font-weight: 900;
		color: #764ba2;
		margin-bottom: 15px;
		letter-spacing: -1px;
	}

	.metric-value.alert-number {
		color: #f59e0b;
	}

	.metric-value.warning {
		color: #ef4444;
	}

	.metric-value.revenue {
		color: #10b981;
	}

	/* Lists */
	.expired-list-advanced,
	.due-list-advanced {
		list-style: none;
		margin: 15px 0;
	}

	.list-item-expired,
	.list-item-due {
		padding: 10px 0;
		border-bottom: 1px solid #f0f0f0;
		font-size: 13px;
		display: flex;
		align-items: center;
		gap: 10px;
		color: #555;
	}

	.list-item-expired:last-child,
	.list-item-due:last-child {
		border-bottom: none;
	}

	.shop-badge {
		display: inline-block;
		background: linear-gradient(135deg, #764ba2, #667eea);
		color: white;
		padding: 4px 10px;
		border-radius: 6px;
		font-weight: 600;
		font-size: 12px;
		min-width: 45px;
		text-align: center;
	}

	.shop-name-text {
		flex: 1;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		font-weight: 500;
	}

	.exp-date {
		color: #999;
		font-size: 12px;
	}

	.list-item-due i {
		color: #764ba2;
		font-size: 8px;
	}

	.view-more,
	.card-text-small {
		font-size: 12px;
		color: #999;
		margin-top: 10px;
		font-weight: 500;
	}

	.view-all-expired {
		display: inline-block;
		margin-top: 10px;
		font-size: 12px;
		padding: 5px 10px;
		border-radius: 8px;
		background: #f59e0b;
		color: #fff;
		text-decoration: none;
		font-weight: 600;
	}

	.defaulter-summary-card {
		display: grid;
		gap: 14px;
		padding-top: 4px;
	}

	.defaulter-summary-grid {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 12px;
	}

	.defaulter-summary-box,
	.defaulter-total-box {
		border-radius: 14px;
		padding: 14px;
		overflow-wrap: anywhere;
		min-height: 116px;
	}

	.due-payment-box {
		background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
		box-shadow: inset 0 0 0 1px rgba(249, 115, 22, 0.08);
	}

	.due-interest-box {
		background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
		box-shadow: inset 0 0 0 1px rgba(220, 38, 38, 0.08);
	}

	.defaulter-total-box {
		background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
		border: 1px solid #93c5fd;
	}

	.summary-title {
		margin: 0 0 8px;
		font-size: 14px;
		font-weight: 700;
		color: #1f2937;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.summary-value,
	.total-value {
		margin: 0;
		font-size: 20px;
		font-weight: 800;
		line-height: 1.1;
		color: #111827;
	}

	.total-value {
		font-size: 26px;
	}

	.summary-note {
		margin: 8px 0 0;
		font-size: 12px;
		color: #475569;
	}

	.view-all-expired:hover {
		background: #d97706;
	}

	.status-ok {
		color: #10b981;
		font-weight: 600;
		font-size: 14px;
	}

	.card-text-bold {
		font-weight: 700;
		color: #1a1a2e;
		margin-bottom: 8px;
		font-size: 16px;
	}

	/* Revenue Status */
	.revenue-status {
		display: flex;
		align-items: center;
		gap: 8px;
		color: #10b981;
		font-weight: 600;
		font-size: 13px;
		margin-top: 12px;
	}

	.revenue-status i {
		font-size: 14px;
	}

	/* Earnings Breakdown */
	.earnings-breakdown {
		background: linear-gradient(135deg, rgba(118, 75, 162, 0.05), rgba(102, 126, 234, 0.05));
		padding: 15px;
		border-radius: 12px;
		margin: 15px 0;
		border: 1px solid rgba(118, 75, 162, 0.1);
	}

	.earnings-item {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 8px 0;
		font-size: 13px;
	}

	.earnings-item:first-child {
		border-bottom: 1px solid rgba(118, 75, 162, 0.1);
		padding-bottom: 10px;
		margin-bottom: 10px;
	}

	.earnings-label {
		color: #666;
		font-weight: 600;
	}

	.earnings-amount {
		font-weight: 700;
		font-size: 14px;
	}

	.earnings-amount.paid {
		color: #10b981;
	}

	.earnings-amount.due {
		color: #f59e0b;
	}

	.status-text {
		font-weight: 600;
		color: #1a1a2e;
		font-size: 14px;
	}

	/* Card Variants */
	.expired-card .card-icon-circle {
		background: linear-gradient(135deg, #ef4444, #f87171);
	}

	.due-card .card-icon-circle {
		background: linear-gradient(135deg, #f59e0b, #fbbf24);
	}

	.defaulter-card .card-icon-circle {
		background: linear-gradient(135deg, #8b5cf6, #a78bfa);
	}

	/* Footer */
	.dashboard-footer {
		text-align: center;
		padding: 30px;
		background: white;
		border-radius: 15px;
		margin-top: 30px;
		border: 1px solid rgba(118, 75, 162, 0.1);
		color: #666;
		font-weight: 500;
		font-size: 14px;
	}

	.dashboard-footer i {
		color: #764ba2;
		margin-right: 8px;
	}

	/* Responsive Design */
	@media (max-width: 1200px) {
		.widgets-row {
			grid-template-columns: repeat(2, 1fr);
		}

		.header-content-wrapper {
			flex-direction: column;
			gap: 20px;
		}

		.header-stats {
			width: 100%;
			justify-content: center;
		}
	}

	@media (max-width: 768px) {
		.advanced-dashboard {
			padding: 20px;
		}

		.dashboard-premium-header {
			padding: 30px;
		}

		.header-title {
			font-size: 24px !important;
		}

		.header-subtitle {
			font-size: 14px;
		}

		.widgets-row {
			grid-template-columns: 1fr;
		}

		.quick-stat {
			min-width: auto;
		}
	}

	.analytics-heading,
	.analytics-panel {
		color: #172033;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	}

	.analytics-heading {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 24px;
		margin-bottom: 22px;
	}

	.analytics-heading h1 {
		margin: 0 0 6px;
		color: #172033;
		font-size: clamp(25px, 3vw, 34px);
		font-weight: 750;
		letter-spacing: -0.8px;
	}

	.analytics-eyebrow {
		margin: 0 0 7px;
		color: #64748b;
		font-size: 10px;
		font-weight: 750;
		letter-spacing: 1.15px;
	}

	.analytics-description,
	.analytics-panel-heading > div > p:last-child {
		margin: 0;
		color: #64748b;
		font-size: 13px;
	}

	.analytics-date {
		display: grid;
		gap: 3px;
		min-width: 170px;
		padding: 12px 16px;
		border: 1px solid #e6ebf2;
		border-radius: 12px;
		background: #fff;
	}

	.analytics-date-label {
		color: #64748b;
		font-size: 9px;
		font-weight: 700;
		letter-spacing: 1px;
	}

	.analytics-date strong {
		color: #172033;
		font-size: 14px;
	}

	.analytics-date > span:last-child {
		color: #94a3b8;
		font-size: 11px;
	}

	.analytics-grid {
		display: grid;
		grid-template-columns: minmax(0, 1.65fr) minmax(300px, 1fr);
		gap: 18px;
		margin-bottom: 28px;
	}

	.collection-summary {
		margin-bottom: 28px;
	}

	.collection-performance-badge {
		flex: 0 0 auto;
		padding: 7px 11px;
		border-radius: 20px;
		background: #eff6ff;
		color: #2563eb;
		font-size: 10px;
		font-weight: 700;
	}

	.collection-performance-badge.above-target {
		background: #ecfdf5;
		color: #047857;
	}

	.collection-performance-badge.below-target {
		background: #fff7ed;
		color: #c2410c;
	}

	.collection-metrics {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 18px;
		margin: 22px 0 18px;
	}

	.collection-metric {
		display: grid;
		gap: 6px;
	}

	.collection-metric > span {
		color: #64748b;
		font-size: 11px;
	}

	.collection-metric strong {
		color: #172033;
		font-size: 20px;
		font-weight: 750;
	}

	.collection-metric .collected-amount {
		color: #0f766e;
	}

	.collection-variance.surplus strong {
		color: #047857;
	}

	.collection-progress-track {
		height: 9px;
		overflow: hidden;
		border-radius: 10px;
		background: #edf1f5;
	}

	.collection-progress-fill {
		height: 100%;
		border-radius: inherit;
		background: linear-gradient(90deg, #2563eb, #14b8a6);
		transition: width 0.3s ease;
	}

	.collection-progress-fill.surplus {
		background: linear-gradient(90deg, #059669, #34d399);
	}

	.collection-progress-caption {
		display: flex;
		justify-content: space-between;
		gap: 12px;
		margin-top: 9px;
		color: #64748b;
		font-size: 10px;
	}

	.analytics-panel {
		min-width: 0;
		padding: 22px 24px 18px;
		border: 1px solid #e6ebf2;
		border-radius: 16px;
		background: #fff;
		box-shadow: 0 5px 18px rgba(15, 23, 42, 0.045);
	}

	.agreement-card-link {
		display: block;
		min-width: 0;
		border-radius: 16px;
		color: inherit;
		text-decoration: none;
	}

	.agreement-card-link:focus-visible {
		outline: 3px solid #2563eb;
		outline-offset: 3px;
	}

	.agreement-card-link:hover .agreement-panel {
		border-color: #bfdbfe;
		box-shadow: 0 10px 24px rgba(37, 99, 235, 0.1);
	}

	.analytics-panel-heading {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 16px;
	}

	.analytics-panel-heading h2 {
		margin: 0 0 5px;
		color: #172033;
		font-size: 17px;
		font-weight: 700;
	}

	.analytics-period-badge {
		flex: 0 0 auto;
		padding: 7px 10px;
		border: 1px solid #e6ebf2;
		border-radius: 20px;
		color: #475569;
		font-size: 10px;
		font-weight: 650;
	}

	.revenue-chart-wrap {
		width: 100%;
		margin-top: 13px;
	}

	#revenue-chart {
		display: block;
		width: 100%;
		height: 250px;
		overflow: visible;
	}

	#revenue-chart text {
		fill: #7b8798;
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		font-size: 11px;
	}

	#revenue-chart .chart-gridline {
		stroke: #edf1f5;
		stroke-width: 1;
	}

	#revenue-chart .chart-bar {
		fill: #2563eb;
	}

	#revenue-chart .chart-bar-current {
		fill: #14b8a6;
	}

	.agreement-chart-layout {
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 12px;
		min-height: 220px;
		margin-top: 5px;
	}

	#agreement-chart {
		flex: 0 0 190px;
		width: 190px;
		height: 190px;
	}

	.agreement-legend {
		display: grid;
		gap: 13px;
		min-width: 110px;
	}

	.agreement-legend-item {
		display: grid;
		grid-template-columns: 9px 1fr auto;
		align-items: center;
		gap: 7px;
		color: #64748b;
		font-size: 11px;
	}

	.agreement-legend-dot {
		width: 8px;
		height: 8px;
		border-radius: 50%;
	}

	.agreement-legend-item strong {
		color: #172033;
		font-size: 12px;
	}

	.dashboard-widgets-advanced {
		gap: 18px;
	}

	.widgets-row {
		gap: 18px;
	}

	.due-summary-row {
		grid-template-columns: minmax(0, 1fr);
	}

	.widget-card-advanced {
		min-height: 0;
		padding: 24px;
		border-color: #e6ebf2;
		box-shadow: 0 5px 18px rgba(15, 23, 42, 0.045);
	}

	.widget-card-advanced:hover {
		transform: translateY(-3px);
		box-shadow: 0 12px 26px rgba(15, 23, 42, 0.09);
	}

	@media (max-width: 1000px) {
		.analytics-grid {
			grid-template-columns: minmax(0, 1fr);
		}

		.agreement-chart-layout {
			justify-content: flex-start;
		}
	}

	@media (max-width: 600px) {
		.analytics-heading {
			align-items: flex-start;
			flex-direction: column;
		}

		.analytics-date {
			width: 100%;
		}

		.analytics-panel {
			padding: 18px 14px 14px;
		}

		.analytics-panel-heading {
			flex-direction: column;
		}

		.collection-metrics {
			grid-template-columns: 1fr 1fr;
			gap: 16px 10px;
		}

		.collection-metric strong {
			font-size: 16px;
		}

		.collection-progress-caption {
			flex-direction: column;
			gap: 4px;
		}

		.agreement-chart-layout {
			gap: 4px;
		}

		#agreement-chart {
			flex-basis: 160px;
			width: 160px;
			height: 160px;
		}

		.agreement-legend {
			gap: 10px;
		}
	}
</style>

@push('scripts')
<script>
	(function () {
		const revenueData = @json($revenueTrend);
		const agreementData = @json($agreementStatusData);
		const svgNamespace = 'http://www.w3.org/2000/svg';

		function addSvgElement(parent, name, attributes, text) {
			const element = document.createElementNS(svgNamespace, name);
			Object.entries(attributes).forEach(([key, value]) => element.setAttribute(key, value));
			if (text !== undefined) element.textContent = text;
			parent.appendChild(element);
			return element;
		}

		function renderRevenueChart() {
			const svg = document.getElementById('revenue-chart');
			const plot = { left: 58, top: 18, width: 560, height: 190 };
			const maxAmount = Math.max(...revenueData.map((item) => item.amount), 1);
			const labelFormat = new Intl.NumberFormat('en-LK', { notation: 'compact', maximumFractionDigits: 1 });

			for (let index = 0; index <= 4; index += 1) {
				const y = plot.top + (plot.height / 4) * index;
				const value = maxAmount * (1 - index / 4);
				addSvgElement(svg, 'line', {
					x1: plot.left,
					x2: plot.left + plot.width,
					y1: y,
					y2: y,
					class: 'chart-gridline'
				});
				addSvgElement(svg, 'text', { x: plot.left - 9, y: y + 4, 'text-anchor': 'end' }, labelFormat.format(value));
			}

			const slotWidth = plot.width / revenueData.length;
			const barWidth = Math.min(48, slotWidth * 0.56);
			revenueData.forEach((item, index) => {
				const barHeight = (item.amount / maxAmount) * (plot.height - 12);
				const x = plot.left + slotWidth * index + (slotWidth - barWidth) / 2;
				const y = plot.top + plot.height - barHeight;
				const bar = addSvgElement(svg, 'rect', {
					x,
					y,
					width: barWidth,
					height: Math.max(barHeight, 2),
					rx: 5,
					class: index === revenueData.length - 1 ? 'chart-bar chart-bar-current' : 'chart-bar',
					'aria-label': item.label + ': Rs ' + new Intl.NumberFormat('en-LK', { maximumFractionDigits: 2 }).format(item.amount)
				});
				addSvgElement(bar, 'title', {}, item.label + ': Rs ' + new Intl.NumberFormat('en-LK', { maximumFractionDigits: 2 }).format(item.amount));
				addSvgElement(svg, 'text', {
					x: plot.left + slotWidth * index + slotWidth / 2,
					y: plot.top + plot.height + 25,
					'text-anchor': 'middle'
				}, item.label);
			});
		}

		function renderAgreementChart() {
			const svg = document.getElementById('agreement-chart');
			const legend = document.getElementById('agreement-legend');
			const center = 110;
			const radius = 94;
			const total = agreementData.reduce((sum, item) => sum + item.value, 0);
			let angle = -Math.PI / 2;

			if (total === 0) {
				addSvgElement(svg, 'circle', { cx: center, cy: center, r: radius, fill: '#e2e8f0' });
			} else if (agreementData.filter((item) => item.value > 0).length === 1) {
				const onlyItem = agreementData.find((item) => item.value > 0);
				addSvgElement(svg, 'circle', { cx: center, cy: center, r: radius, fill: onlyItem.color });
			} else {
				agreementData.filter((item) => item.value > 0).forEach((item) => {
					const nextAngle = angle + (item.value / total) * Math.PI * 2;
					const startX = center + radius * Math.cos(angle);
					const startY = center + radius * Math.sin(angle);
					const endX = center + radius * Math.cos(nextAngle);
					const endY = center + radius * Math.sin(nextAngle);
					const largeArc = nextAngle - angle > Math.PI ? 1 : 0;
					const path = 'M ' + center + ' ' + center + ' L ' + startX + ' ' + startY
						+ ' A ' + radius + ' ' + radius + ' 0 ' + largeArc + ' 1 ' + endX + ' ' + endY + ' Z';
					const slice = addSvgElement(svg, 'path', {
						d: path,
						fill: item.color,
						stroke: '#fff',
						'stroke-width': 2
					});
					addSvgElement(slice, 'title', {}, item.label + ': ' + item.value + ' shops');
					angle = nextAngle;
				});
			}

			agreementData.forEach((item) => {
				const row = document.createElement('div');
				row.className = 'agreement-legend-item';
				const dot = document.createElement('span');
				dot.className = 'agreement-legend-dot';
				dot.style.backgroundColor = item.color;
				const label = document.createElement('span');
				label.textContent = item.label;
				const value = document.createElement('strong');
				value.textContent = item.value.toLocaleString();
				row.append(dot, label, value);
				legend.appendChild(row);
			});
		}

		renderRevenueChart();
		renderAgreementChart();
	})();
</script>
@endpush
@endsection
