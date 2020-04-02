import React from 'react';
import ContentHeader from '../content-header';
import Breadcrumbs from '../common/Breadcrumbs';
import LoadingContent from '../loading-content';
import Table, { SortOrder } from '../table/table';
import { DetailsProps } from '../details';
import $ from 'jquery';

interface TableBuilder {
    className?: string,
    headers: string[],
    defaultSortColumnIndex: number,
    defaultSortOrder: SortOrder,
    buildPureValuesToSort: (dataItem: object) => (number|string)[],
    buildRowCells: (dataItem: object) => any[],
    buildRowDetails: (dataItem: object) => DetailsProps[]
}

interface HistoryProps {
    breadcrumbsNamePart: string,
    apiDataUrl: string,
    tableBuilder: TableBuilder
}

interface HistoryState {
    isLoaded: boolean,
    data: object[]
}

class History extends React.Component<HistoryProps, HistoryState> {
    public constructor(props: HistoryProps) {
        super(props);
        this.state = {
            isLoaded: false,
            data: []
        }
    }

    public componentDidMount(): void {
        $.ajax({
            url: this.props.apiDataUrl,
            dataType: 'json',
            success: (result: object[]) => {
                this.setState({
                    data: result,
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
                <Breadcrumbs items={[
                    { name: 'Мониторинг' },
                    { name: props.breadcrumbsNamePart },
                    { name: `История ${state.isLoaded ? '(' + state.data.length + ')' : ''}` }
                ]} />
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

export default History;