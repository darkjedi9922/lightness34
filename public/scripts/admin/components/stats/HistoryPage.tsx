import React from 'react';
import ContentHeader, { ContentHeaderGroup } from '../content/ContentHeader';
import Breadcrumbs from '../common/Breadcrumbs';
import LoadingContent from '../content/LoadingContent';
import Table, { SortOrder } from '../table/Table';
import { DetailsProps } from '../table/TableDetails';
import $ from 'jquery';
import parseUrl from 'url-parse';

interface TableBuilder {
    className?: string,
    headers: string[],
    defaultSortColumnIndex: number,
    defaultSortOrder: SortOrder,
    buildPureValuesToSort: (dataItem: object) => (number|string)[],
    buildRowCells: (dataItem: object) => any[],
    buildRowDetails: (dataItem: object) => DetailsProps[]
}

interface ApiResult {
    list: object[],
    countAll: number,
    pagerHtml: string
}

interface Props {
    breadcrumbsNamePart: string,
    apiDataUrl: string,
    tableBuilder: TableBuilder
}

interface State {
    isLoaded: boolean,
    data: object[],
    countAll: number,
    pagerHtml: string
}

class HistoryPage extends React.Component<Props, State> {
    public constructor(props: Props) {
        super(props);
        this.state = {
            isLoaded: false,
            data: [],
            countAll: 0,
            pagerHtml: null
        }
    }

    public componentDidMount(): void {
        $.ajax({
            url: this.props.apiDataUrl,
            data: {
                p: parseUrl(window.location.search, true).query['p'] || 1
            },
            dataType: 'json',
            success: (result: ApiResult) => {
                this.setState({
                    data: result.list,
                    countAll: result.countAll,
                    pagerHtml: result.pagerHtml,
                    isLoaded: true
                })
            }
        })
    }

    public render(): React.ReactNode {
        const state = this.state;
        const props = this.props;
        const builder = props.tableBuilder;
        return <>
            <ContentHeader>
                <ContentHeaderGroup>
                    <Breadcrumbs items={[
                        { name: 'Мониторинг' },
                        { name: props.breadcrumbsNamePart },
                        { name: `История ${state.isLoaded ? '(' + state.countAll + ')' : ''}` }
                    ]} />
                </ContentHeaderGroup>
                {state.isLoaded &&
                    <ContentHeaderGroup>
                        <div dangerouslySetInnerHTML={{ __html: state.pagerHtml}} />
                    </ContentHeaderGroup>
                }
            </ContentHeader>
            <LoadingContent>
                {state.isLoaded && <div className="box box--table">
                    <Table
                        className={builder.className}
                        collapsable={true}
                        headers={builder.headers}
                        sort={{
                            defaultCellIndex: builder.defaultSortColumnIndex,
                            defaultOrder: builder.defaultSortOrder,
                            isAlreadySorted: true
                        }}
                        items={state.data.map((item) => ({
                            pureCellsToSort: builder.buildPureValuesToSort(item),
                            cells: builder.buildRowCells(item),
                            details: builder.buildRowDetails(item)
                        }))}
                    />
                </div>}
            </LoadingContent>
        </>
    }
}

export default HistoryPage;