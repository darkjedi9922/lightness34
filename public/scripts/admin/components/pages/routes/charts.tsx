import React from 'react';
import ContentHeader, { ContentHeaderGroup } from '../../content-header';
import LoadingContent from '../../loading-content';
import Breadcrumbs from '../../common/Breadcrumbs';
import $ from 'jquery';
import MultipleChart, { TimeIntervalValues, SortColumn } from '../../charts/MultipleChart';
import SingleChart, { TimeIntervalValue } from '../../charts/SingleChart';
import { SortOrder } from '../../table/table';

enum SecondInterval {
    HOUR = 60 * 60,
    DAY = HOUR * 24,
    WEEK = DAY * 7,
    MONTH = DAY * 30
}

interface ChartData {
    title: string,
    type: 'single' | 'multiple',
    apiUrl: string,
    secondInterval: SecondInterval
    intervalCount: number,
}

interface SingleChartData extends ChartData {
    intervals: TimeIntervalValue[]
}

interface MultipleChartData extends ChartData {
    intervals: TimeIntervalValues[],
    limit: number,
    sortField: SortColumn,
    sortOrder: SortOrder,
    columnUpdating?: SortColumn
}

interface RoutesChartsState {
    charts: (SingleChartData|MultipleChartData)[]
}

interface RouteCountsAPIResultItem extends TimeIntervalValue {}

interface RoutesMultipleStatsAPIResult {
    [url: string]: [{
        value?: number,
        time: string,
        timestamp: number
    }]
}

class RoutesCharts extends React.Component<{}, RoutesChartsState> {
    public constructor(props) {
        super(props);
        this.state = {
            charts: [{
                title: 'Общее количество',
                type: 'single',
                apiUrl: '/api/stats/routes/summary',
                intervals: null,
                secondInterval: SecondInterval.DAY,
                intervalCount: 10
            } as SingleChartData, {
                title: 'Макс. количество',
                type: 'multiple',
                apiUrl: '/api/stats/routes/count',
                intervals: null,
                secondInterval: SecondInterval.DAY,
                intervalCount: 10,
                limit: 5,
                sortField: SortColumn.MAX,
                sortOrder: SortOrder.DESC,
                columnUpdating: null
            } as MultipleChartData, {
                title: 'Макс. время',
                type: 'multiple',
                apiUrl: '/api/stats/routes/durations',
                intervals: null,
                secondInterval: SecondInterval.DAY,
                intervalCount: 10,
                limit: 5,
                sortField: SortColumn.MAX,
                sortOrder: SortOrder.DESC,
                columnUpdating: null
            } as MultipleChartData]
        };
    }

    public componentDidMount() {
        for (let i = 0; i < this.state.charts.length; i++) {
            const chart = this.state.charts[i];
            this.loadChartData(chart);
        }
    }

    public render(): React.ReactNode {
        const basePaths = [
            { 'name': 'Мониторинг' },
            { 'name': 'Маршруты' },
            { 'name': 'Статистика' }
        ];
        return this.areAllChartsLoaded()
            ? this.state.charts.map((chart, index) => <div key={index}>
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, { 'name': chart.title }]} />
                </ContentHeader>
                {chart.type === 'single'
                    ? <SingleChart
                        intervals={chart.intervals as TimeIntervalValue[]}
                    />
                    : <MultipleChart
                        intervals={chart.intervals as TimeIntervalValues[]}
                        sortColumn={(chart as MultipleChartData).sortField}
                        sortOrder={(chart as MultipleChartData).sortOrder}
                        columnUpdating={(chart as MultipleChartData).columnUpdating}
                        onSort={(column, order) => {
                            return this.onMultipleChartSort(index, column, order)
                        }}
                    />
                }
            </div>)
            : <>
                <ContentHeader>
                    <Breadcrumbs items={basePaths} />
                </ContentHeader>
                <LoadingContent></LoadingContent>
            </>
    }

    private loadChartData(chart: SingleChartData|MultipleChartData) {
        let promise: Promise<TimeIntervalValue[] | TimeIntervalValues[]> = null;
        switch (chart.type) {
            case 'single':
                promise = this.loadSingleStats(chart as SingleChartData);
                break;
            case 'multiple':
                promise = this.loadMultipleStats(chart as MultipleChartData);
                break;
        }
        const chartIndex = this.state.charts.indexOf(chart);
        promise.then((result) => this.updateChartIntervals(chartIndex, result));
    }

    private updateChartIntervals(
        chartIndex: number,
        newIntervals: TimeIntervalValue[] | TimeIntervalValues[]
    ): void {
        this.setState((state) => {
            const newState = { ...state };
            const newChart = newState.charts[chartIndex];
            newChart.intervals = newIntervals;
            if (newChart.type === 'multiple') {
                (newChart as MultipleChartData).columnUpdating = null;
            }
            return newState;
        })
    }

    private loadSingleStats(
        chart: SingleChartData
    ): Promise<TimeIntervalValue[]> {
        return new Promise<TimeIntervalValue[]>((resolve, reject) => {
            $.ajax({
                url: chart.apiUrl,
                dataType: 'json',
                success: (result: RouteCountsAPIResultItem[]) => {
                    resolve(result);
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    reject(chart.apiUrl + ' error:' + errorThrown);
                }
            })
        })
    }

    private loadMultipleStats(
        chart: MultipleChartData
    ): Promise<TimeIntervalValues[]> {
        return new Promise<TimeIntervalValues[]>((resolve, reject) => {
            $.ajax({
                url: chart.apiUrl,
                method: 'get',
                data: {
                    limit: chart.limit,
                    field: chart.sortField,
                    order: chart.sortOrder,
                    intervals: chart.intervalCount,
                    sec_interval: chart.secondInterval
                },
                dataType: 'json',
                success: (result: RoutesMultipleStatsAPIResult) => {
                    const intervals: TimeIntervalValues[] = [];
                    for (const objectName in result) {
                        if (result.hasOwnProperty(objectName)) {
                            const data = result[objectName];
                            // Все objectName в ответе имеют одинаковое количество
                            // одинаковых временных интервалов.
                            for (let i = 0; i < data.length; i++) {
                                const valueData = data[i];
                                // Поэтому будем обновлять те же интервалы,
                                // добавляя в них каждый новый objectName.
                                if (!intervals[i]) intervals[i] = {
                                    time: valueData.time,
                                    values: {}
                                }
                                intervals[i].values[objectName] = valueData.value;
                            }
                        }
                    }
                    resolve(intervals);
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    reject(chart.apiUrl + ' error:' + errorThrown);
                }
            })
        })
    }

    private areAllChartsLoaded(): boolean {
        return this.state.charts.findIndex((chart) => 
            chart.intervals === null
        ) === -1;
    }

    private onMultipleChartSort(
        chartIndexInState: number,
        column: SortColumn,
        order: SortOrder
    ): void {
        this.setState((state) => {
            const newState = { ...state };
            const chart = newState.charts[chartIndexInState] as MultipleChartData;
            chart.columnUpdating = column;
            return newState;
        }, () => {
            const chart = this.state.charts[chartIndexInState] as MultipleChartData;
            chart.sortField = column;
            chart.sortOrder = order;
            this.loadChartData(chart);
        })
    }
}

export default RoutesCharts;