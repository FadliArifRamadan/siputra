import './bootstrap';

// FullCalendar
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

// Chart.js
import Chart from 'chart.js/auto';

// Alpine.js
import Alpine from 'alpinejs';

window.Chart = Chart;
window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {

        const calendar = new Calendar(calendarEl, {
            plugins: [
                dayGridPlugin,
                timeGridPlugin,
                interactionPlugin
            ],

            initialView: 'dayGridMonth',

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },

            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },

            locale: 'id',
            timeZone: 'local',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },

            events: '/api/peminjaman-events',

            eventColor: '#0f8a82',
            eventTextColor: '#ffffff',

            height: 'auto',

            slotMinTime: '06:00:00',
            slotMaxTime: '22:00:00',

            eventClick: function(info) {

                info.jsEvent.preventDefault();

                const event = info.event;
                const props = event.extendedProps;

                const startDate = props.waktu_mulai_formatted || (event.start
                    ? event.start.toLocaleString('id-ID')
                    : '-');

                const endDate = props.waktu_selesai_formatted || (event.end
                    ? event.end.toLocaleString('id-ID')
                    : '-');

                const status = props.status || 'Disetujui';

                const modal = document.getElementById('eventModal');
                const content = document.getElementById('eventDetailContent');

                if (!modal || !content) {
                    console.error('Modal tidak ditemukan.');
                    return;
                }

                content.innerHTML = `
                    <div class="event-title">
                        📌 ${event.title}
                    </div>

                    <div class="event-info">

                        <p>
                            <span class="label">🕐 Mulai:</span>
                            <span class="value">${startDate}</span>
                        </p>

                        <p>
                            <span class="label">🕐 Selesai:</span>
                            <span class="value">${endDate}</span>
                        </p>

                        <hr class="event-divider">

                        <p>
                            <span class="label">📦 Fasilitas:</span>
                            <span class="value">${props.fasilitas ?? '-'}</span>
                        </p>

                        <p>
                            <span class="label">👤 Peminjam:</span>
                            <span class="value">${props.peminjam ?? '-'}</span>
                        </p>

                        <p>
                            <span class="label">📝 Keperluan:</span>
                            <span class="value">${props.keperluan ?? '-'}</span>
                        </p>

                        <p>
                            <span class="label">📊 Status:</span>
                            <span class="event-status ${status.toLowerCase()}">
                                ${status}
                            </span>
                        </p>

                    </div>
                `;

                modal.classList.add('active');
            }

        });

        calendar.render();
    }

    // ==========================
    // Tutup modal
    // ==========================

    const modal = document.getElementById('eventModal');

    if (modal) {

        modal.addEventListener('click', function(e) {

            if (e.target === modal) {
                closeEventModal();
            }

        });

    }

    document.addEventListener('keydown', function(e) {

        if (e.key === 'Escape') {
            closeEventModal();
        }

    });

});

window.closeEventModal = function () {

    const modal = document.getElementById('eventModal');

    if (modal) {
        modal.classList.remove('active');
    }

}

Alpine.start();