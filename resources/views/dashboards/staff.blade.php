{{--
    staff.blade.php is no longer rendered by the controller.
    The `DashboardController::staff()` method now returns the same
    view and data as the manager dashboard, so all staff users see the
    full analytics cards.

    This file is kept for reference only; it can safely be deleted
    once you're confident that the staff route points to the manager
    view (which currently lives at resources/views/dashboards/manager.blade.php).
--}}
