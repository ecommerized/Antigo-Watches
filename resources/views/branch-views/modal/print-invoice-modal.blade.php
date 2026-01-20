<div class="modal fade" id="print-invoice" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{translate('print')}} {{translate('invoice')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-2 justify-content-center">
                    <input type="button" class="btn btn-primary non-printable print-div-button"
                           data-name="printableArea"
                           value="Proceed, If thermal printer is ready."/>
                    <a href="{{url()->previous()}}" class="btn btn-danger non-printable">{{ translate('Back') }}</a>
                </div>
                <hr class="non-printable">
                <div class="row" id="printableArea">

                </div>
            </div>
        </div>
    </div>
</div>
