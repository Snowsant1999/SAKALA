const sidebarNavigation = document.querySelector('.sidebar-nav');

const reservationCourseSelect = document.querySelector('#course_id');
const reservationClassSelect = document.querySelector('#course_class_id');

if (reservationCourseSelect && reservationClassSelect) {
	const reservationClassOptions = Array.from(reservationClassSelect.options)
		.filter((option) => option.dataset.courseId);

	const updateReservationClasses = (resetSelection = false) => {
		const selectedCourseId = reservationCourseSelect.value;

		if (resetSelection) {
			reservationClassSelect.value = '';
		}

		reservationClassOptions.forEach((option) => {
			const isAvailable = selectedCourseId !== '' && option.dataset.courseId === selectedCourseId;

			option.hidden = !isAvailable;
			option.disabled = !isAvailable;
		});

		const selectedClass = reservationClassSelect.selectedOptions[0];

		if (selectedClass?.dataset.courseId && selectedClass.dataset.courseId !== selectedCourseId) {
			reservationClassSelect.value = '';
		}
	};

	reservationCourseSelect.addEventListener('change', () => updateReservationClasses(true));
	updateReservationClasses();
}

if (sidebarNavigation) {
	const scrollStorageKey = 'sakala-sidebar-scroll';
	const savedScrollTop = sessionStorage.getItem(scrollStorageKey);

	if (savedScrollTop !== null) {
		window.requestAnimationFrame(() => {
			sidebarNavigation.scrollTop = Number(savedScrollTop) || 0;
			sessionStorage.removeItem(scrollStorageKey);
		});
	}

	sidebarNavigation.addEventListener('click', (event) => {
		const link = event.target.closest('a[href]');

		if (!link || link.target === '_blank' || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
			return;
		}

		const destination = new URL(link.href, window.location.href);

		if (destination.origin === window.location.origin) {
			sessionStorage.setItem(scrollStorageKey, String(sidebarNavigation.scrollTop));
		}
	});
}

if (document.querySelector('[data-dashboard-live]')) {
	let dashboardRefreshInFlight = false;

	const refreshDashboard = async () => {
		if (document.hidden || dashboardRefreshInFlight) {
			return;
		}

		dashboardRefreshInFlight = true;

		try {
			const response = await fetch(window.location.href, {
				headers: { 'X-Requested-With': 'XMLHttpRequest' },
			});

			if (!response.ok || response.redirected) {
				return;
			}

			const refreshedPage = new DOMParser().parseFromString(await response.text(), 'text/html');
			const refreshedContent = refreshedPage.querySelector('[data-dashboard-live]');
			const currentContent = document.querySelector('[data-dashboard-live]');

			if (refreshedContent && currentContent) {
				currentContent.replaceWith(refreshedContent);
			}
		} catch (error) {
			console.error('Dashboard refresh failed:', error);
		} finally {
			dashboardRefreshInFlight = false;
		}
	};

	window.setInterval(refreshDashboard, 30000);
	document.addEventListener('visibilitychange', () => {
		if (!document.hidden) {
			refreshDashboard();
		}
	});
}

document.addEventListener('DOMContentLoaded', () => {
	if (typeof window.loadNotifications !== 'function') {
		return;
	}

	window.loadNotifications();
	window.setInterval(() => {
		if (!document.hidden) {
			window.loadNotifications();
		}
	}, 30000);
});

document.addEventListener('visibilitychange', () => {
	if (!document.hidden && typeof window.loadNotifications === 'function') {
		window.loadNotifications();
	}
});
