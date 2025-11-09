import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';

// Wait for DOM ready
function initCalendar() {
    const el = document.getElementById('workflow-fullcalendar');
    if (!el) return; // not on this page

    el.style.minHeight = '600px';

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek,dayGridDay' },
        // Use a custom fetch so we include credentials (session cookie) and handle errors gracefully
        events: function(fetchInfo, successCallback, failureCallback) {
            const url = '/api/tasks/events?start=' + encodeURIComponent(fetchInfo.startStr) + '&end=' + encodeURIComponent(fetchInfo.endStr);
            fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(r => {
                    if (!r.ok) throw new Error('Failed to fetch events: ' + r.status);
                    return r.json();
                })
                .then(data => {
                    // FullCalendar expects an array of event objects
                    successCallback(Array.isArray(data) ? data : []);
                })
                .catch(err => {
                    console.error('calendar events fetch error', err);
                    failureCallback(err);
                });
        },
        // When a user clicks an event, try to open it in the central modal (AJAX), fallback to navigation
        eventClick: function(info) {
            try {
                info.jsEvent && info.jsEvent.preventDefault();
                if (info.event && info.event.url) {
                    // Use existing modal loader on the page
                    if (typeof loadIntoModal === 'function') {
                        loadIntoModal(info.event.url, 'Task Details');
                    } else {
                        // Fallback to navigating
                        window.location.href = info.event.url;
                    }
                }
            } catch (e) {
                console.error('calendar eventClick error', e);
            }
        },
        // Allow creating a new task by clicking a day on the calendar
        dateClick: function(info) {
            try {
                // If the page provides a modal-based creation UI, open it and prefill the due date
                if (typeof showTaskModal === 'function') {
                    showTaskModal();
                    // Delay to allow modal/form to be present
                    setTimeout(function(){
                        try {
                            const due = document.getElementById('task-due');
                            if (due) due.value = info.dateStr;
                            const form = document.getElementById('task-create-form');
                            if (form) { form.removeAttribute('data-editing'); }
                        } catch (e) { /* ignore */ }
                    }, 80);
                } else if (typeof loadIntoModal === 'function') {
                    // Try to open a create form endpoint if available
                    loadIntoModal('/tasks/create', 'Create Task');
                } else {
                    // As a last resort, prompt with a simple quick-create
                    const title = prompt('New task title for ' + info.dateStr);
                    if (title && title.trim()) {
                        fetch('/tasks', {
                            method: 'POST',
                            headers: { 'Accept':'application/json', 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                            credentials: 'same-origin',
                            body: JSON.stringify({ title: title.trim(), due_date: info.dateStr })
                        }).then(r=>r.json()).then(data=>{
                            if (data && data.success) {
                                // reload events
                                calendar.refetchEvents();
                                alert('Task created');
                            } else {
                                alert('Failed to create task');
                            }
                        }).catch(e=>{ console.error(e); alert('Failed to create task'); });
                    }
                }
            } catch (e) {
                console.error('dateClick error', e);
            }
        },
        dayMaxEventRows: true,
    });

    calendar.render();
    // expose for other scripts to refresh events after CRUD operations
    try {
        window.workflowCalendar = calendar;
        window.refetchWorkflowCalendar = function() { try { calendar.refetchEvents(); } catch(e) { console.error(e); } };
    } catch (e) { /* ignore in environments without window */ }
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initCalendar);
else initCalendar();
