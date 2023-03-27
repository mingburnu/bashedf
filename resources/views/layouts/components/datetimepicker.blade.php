<div class="col-md-auto">{{__('ui.datetime')}}</div>

<div class="form-group">
    <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
        <label for="startDateTime"></label>
        <input type="text" class="form-control datetimepicker-input pointer-events-none"
               data-target="#datetimepicker1" id="startDateTime" name="startDateTime"/>
        <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
</div>

<div class="col-md-auto">
    <i class="fa fa-arrow-right"></i>
</div>

<div class="form-group">
    <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
        <label for="endDateTime"></label>
        <input type="text" class="form-control datetimepicker-input pointer-events-none"
               data-target="#datetimepicker2" id="endDateTime" name="endDateTime"/>
        <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
</div>

<div class="col-md-auto">
    <input type="button" value="{{__('ui.today')}}" class="btn btn-secondary"
           onclick="getPeriodDate('today')"/>
</div>
<div class="col-md-auto">
    <input type="button" value="{{__('ui.yesterday')}}" class="btn btn-secondary"
           onclick="getPeriodDate('yesterday')"/>
</div>
<div class="col-md-auto">
    <input type="button" value="{{__('ui.this_week')}}" class="btn btn-secondary"
           onclick="getPeriodDate('week')"/>
</div>
<div class="col-md-auto">
    <input type="button" value="{{__('ui.this_month')}}" class="btn btn-secondary"
           onclick="getPeriodDate('month')"/>
</div>
<div class="col-md-auto">
    <input type="button" value="{{__('ui.all')}}" class="btn btn-secondary"
           onclick="getPeriodDate('')"/>
</div>
@push('js')
    <script>
        $(document).ready(function () {
            $('div.input-group').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
                sideBySide: true
            });
        });
    </script>
@endpush