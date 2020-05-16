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
    mapHeadersToSortFields: string[],
    defaultSortColumnIndex: number,
    defaultSortOrder: SortOrder,
    buildRowCells: (dataItem: object) => any[],
    buildRowDetails: (dataItem: object) => DetailsProps[],
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
    isUpdating: boolean,
    data: object[],
    countAll: number,
    pagerHtml: string,
    sortField: string,
    sortOrder: SortOrder
}

class HistoryPage extends React.Component<Props, State> {
    public constructor(props: Props) {
        super(props);
        const tb = props.tableBuilder;
        this.state = {
            isLoaded: false,
            isUpdating: false,
            data: [],
            countAll: 0,
            pagerHtml: null,
            sortField: tb.mapHeadersToSortFields[tb.defaultSortColumnIndex],
            sortOrder: props.tableBuilder.defaultSortOrder
        }

        this.onTableSort = this.onTableSort.bind(this);
    }

    public componentDidMount(): void {
        this.loadValues();
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
                        headers={builder.headers.map((header, index) => {
                            const sortFields = props.tableBuilder.mapHeadersToSortFields;
                            const isUpdating = state.isUpdating && index === sortFields.indexOf(state.sortField);
                            return <span>
                                {header} {isUpdating && <i className="icon-spin1 animate-spin table__loading"/>}
                            </span>
                        })}
                        sort={{
                            defaultCellIndex: props.tableBuilder.mapHeadersToSortFields.indexOf(state.sortField),
                            defaultOrder: state.sortOrder,
                            isAlreadySorted: true,
                            onSort: this.onTableSort
                        }}
                        items={state.data.map((item) => ({
                            cells: builder.buildRowCells(item),
                            details: builder.buildRowDetails(item)
                        }))}
                    />
                </div>}
            </LoadingContent>
        </>
    }

    private loadValues(): void {
        $.ajax({
            url: this.props.apiDataUrl,
            data: {
                sort: this.state.sortField,
                order: this.state.sortOrder,
                p: parseUrl(window.location.search, true).query['p'] || 1
            },
            dataType: 'json',
            success: (result: ApiResult) => {
                this.setState({
                    data: result.list,
                    countAll: result.countAll,
                    pagerHtml: result.pagerHtml,
                    isLoaded: true,
                    isUpdating: false
                })
            }
        })
    }

    private onTableSort(column: number, order: SortOrder): void {
        if (this.state.isUpdating) return;
        this.setState({
            isUpdating: true,
            sortField: this.props.tableBuilder.mapHeadersToSortFields[column],
            sortOrder: order
        }, this.loadValues.bind(this));
    }
}

export default HistoryPage;