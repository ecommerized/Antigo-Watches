@if ($paginator->total() > 0)
    <div class="card-footer d-flex align-items-center flex-wrap gap-2 justify-content-between">
        <div class="d-flex align-items-center justify-content-center gap-3">
            <form id="perPageForm" method="GET" class="mb-0">
                @foreach (request()->except('per_page', 'page') as $name => $value)
                    @if (is_array($value))
                        @foreach ($value as $v)
                            <input type="hidden" name="{{ $name }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endif
                @endforeach

                <div class="d-inline-block">
                    <select name="per_page" class="custom-select w-auto h-100 py-1 rounded fs-12"
                        onchange="this.form.submit()">
                        @foreach (Helpers::paginateValueNumberOptions($perPage) as $paginateOptions)
                            <option value="{{ $paginateOptions }}" @selected($perPage == $paginateOptions)>{{ $paginateOptions }}
                                {{ translate('items') }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <p class="text-record fs-12px mb-0">
                {{ translate('Showing') }} {{ $paginator->firstItem() }} {{ translate('To') }}
                {{ $paginator->lastItem() }} {{ translate('Of') }} {{ $paginator->total() }}
                {{ translate('Records') }}
            </p>
        </div>

        @if ($paginator->hasPages())
            <div class="d-flex justify-content-center justify-content-sm-end">
                <div class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->url(1) ?: '#' }}" aria-label="First Page URL"
                        aria-disabled="{{ $paginator->onFirstPage() ? 'true' : 'false' }}" rel="first">
                        <svg width="9" height="9" viewBox="0 0 9 9" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M4.45225 0.416895c.10759-.000673.21293.033172.30259.097223.08965.06405.15957.155403.20082.262412.04126.107009.05199.224829.03083.338429-.02116.1136-.07326.21784-.14965.29943L1.966 4.50023l2.87084 3.08583c.08874.11159.13511.25514.12984.40195-.00527.14681-.06178.28608-.15825.38996-.09646.10389-.22578.16475-.36211.17042-.13632.00567-.26961-.04426-.37323-.13983L.823086 4.90856c-.100886-.10929-.157513-.25714-.157513-.41125 0-.15411.056627-.30195.157513-.41125L4.07309.586062c.10089-.107768.23706-.168522.37916-.169167Z"
                                fill="#6A727A" />
                            <path
                                d="M7.70225.416883c.10759-.000673.21293.033173.30259.097224.08965.06405.15957.155403.20082.262412.04126.107009.05199.224821.03083.338431-.02116.1136-.07326.21784-.14965.29943L5.216 4.50022l2.87084 3.08583c.10199.10984.1593.25882.1593.41417s-.05731.30898-.1593.41882c-.102.10985-.24034.17156-.38459.17156s-.28258-.06171-.38458-.17156L4.06767 4.91438c-.10089-.10929-.15751-.25714-.15751-.41125 0-.15411.05662-.30195.15751-.41125l3.25-3.5c.05017-.055118.10999-.098966.17601-.129006.06601-.03004.13691-.045673.20857-.045994Z"
                                fill="#6A727A" />
                        </svg>
                    </a>
                </div>

                <div class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}"
                        aria-label="Previous Page URL"
                        aria-disabled="{{ $paginator->onFirstPage() ? 'true' : 'false' }}" rel="prev">
                        <i class="tio-chevron-left"></i>
                    </a>
                </div>

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <div class="page-item disabled"><span class="page-link">{{ $element }}</span></div>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <div class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </div>
                        @endforeach
                    @endif
                @endforeach

                <div class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}" aria-label="Next Page URL"
                        aria-disabled="{{ $paginator->hasMorePages() ? 'false' : 'true' }}" rel="next">
                        <i class="tio-chevron-right"></i>
                    </a>
                </div>

                <div class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) ?: '#' }}"
                        aria-label="Last Page URL" aria-disabled="{{ $paginator->hasMorePages() ? 'false' : 'true' }}"
                        rel="last">

                        <svg width="8" height="9" viewBox="0 0 8 9" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3.99989.416895c-.10758-.000673-.21292.033172-.30258.097223-.08965.06405-.15957.155403-.20082.262412-.04126.107009-.05199.224829-.03083.338429.02116.1136.07326.21784.14965.29943L6.48615 4.50023 3.61531 7.58606c-.08874.11159-.13511.25514-.12984.40195.00526.14681.06178.28608.15824.38996.09647.10389.22579.16475.36212.17042.13633.00567.26962-.04426.37324-.13983l3.25-3.5c.10089-.10929.15751-.25714.15751-.41125 0-.15411-.05662-.30195-.15751-.41125l-3.25-3.5c-.10089-.107768-.23706-.168522-.37917-.169167Z"
                                fill="#6A727A" />
                            <path
                                d="M.749894.416883C.642306.41621.536969.450056.447312.514107.357656.578157.287742.66951.246485.776519.205228.883528.194497 1.00134.21566 1.11495c.021162.1136.07326.21784.149651.29943L3.23615 4.50022.365312 7.58605C.263314 7.69589.206012 7.84487.206012 8.00022c0 .15534.057302.30432.1593.41416.101998.10985.240336.17156.384583.17156.144247 0 .282585-.06171.384583-.17156l3.25-3.5c.10089-.10929.15751-.25714.15751-.41125 0-.15411-.05662-.30195-.15751-.41125l-3.25-3.5C1.08431.536765 1.02449.492917.958472.462877.892456.432837.821561.417204.749894.416883Z"
                                fill="#6A727A" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
@endif
