<div id="timesheetSection">
    <div class="card punch-status">
        <div class="card-body">
            <h5 class="card-title">Timesheet <small class="text-muted" id="punchDate">{{ date('dS M Y') }}</small>
            </h5>

            <div class="punch-det">
                <h6>Punch In at</h6>
                <p id="punchInTime">{{ $shift_record ? $shift_record->check_in : '--' }}</p>
            </div>

            <div class="progress-container">
                <svg class="progress-circle" viewBox="0 0 110 110">
                    <circle cx="55" cy="55" r="50" fill="none" stroke="#eee" stroke-width="8"></circle>
                    <circle class="progress-bar" cx="55" cy="55" r="50" stroke-dasharray="314" stroke-dashoffset="314"
                        fill="none" stroke="#3498db" stroke-width="8" stroke-linecap="round"></circle>
                </svg>
                <div class="time-counter" id="timeCounter">0:00 hrs</div>
            </div>

            <div class="punch-btn-section d-flex justify-content-center mt-3">
                <!-- <button id="punchInBtn" class="btn btn-primary punch-btn mx-2">Punch In</button> -->

                @php
                    $shift_out_data = isset($shift_record) ? $shift_record->shift_out : null;
                    $shift_out_time = $shift_out_data ? \Carbon\Carbon::parse($shift_out_data) : null;
                    $serverTime = \Carbon\Carbon::parse($serverTime);
                @endphp

                @if($shift_out_time)
                    @if($serverTime->greaterThanOrEqualTo($shift_out_time))
                        <button id="punchOutBtn" class="btn btn-primary punch-btn mx-2">Punch Out</button>
                    @else
                        <button class="btn btn-primary punch-btn mx-2" disabled>Punch Out</button>
                        <button id="emergencyCheckOutBtn" class="btn btn-danger punch-btn mx-2">Emergency PunchOut</button>
                    @endif
                @else
                    <button id="punchInBtn" class="btn btn-primary punch-btn mx-2">Punch In</button>
                @endif
            </div>
        </div>
    </div>
</div>