import React from 'react';
import ContentHeader from '../../content-header';
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
                apiUrl: '/api/stats/counts/route',
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
                sortOrder: SortOrder.DESC
            } as MultipleChartData, {
                title: 'Макс. время',
                type: 'multiple',
                apiUrl: '/api/stats/routes/durations',
                intervals: null,
                secondInterval: SecondInterval.DAY,
                intervalCount: 10,
                limit: 5,
                sortField: SortColumn.MAX,
                sortOrder: SortOrder.DESC
            }]
        };
    }

    public componentDidMount() {
        for (let i = 0; i < this.state.charts.length; i++) {
            const chartData = this.state.charts[i];
            const update = (result: TimeIntervalValue[] |TimeIntervalValues[]) => {
                this.setState((state) => {
                    const newState = { ...state };
                    newState.charts[i].intervals = result;
                    return newState;
                })
            }
            switch (chartData.type) {
                case 'single':
                    this.loadCountStatistics(chartData.apiUrl).then(update);
                    break;
                case 'multiple':
                    this.loadParamStatistics(chartData.apiUrl).then(update);
                    break;
            }
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
                        onSort={(column, order) => this.onMultipleChartSort(
                            index, column, order
                        )}
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

    private loadCountStatistics(apiUrl: string): Promise<TimeIntervalValue[]> {
        return new Promise<TimeIntervalValue[]>((resolve, reject) => {
            $.ajax({
                url: apiUrl,
                dataType: 'json',
                success: (result: RouteCountsAPIResultItem[]) => {
                    resolve(result);
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    reject(apiUrl + ' error:' + errorThrown);
                }
            })
        })
    }

    private loadParamStatistics(apiUrl: string): Promise<TimeIntervalValues[]> {
        return new Promise<TimeIntervalValues[]>((resolve, reject) => {
            $.ajax({
                url: apiUrl,
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
                    reject(apiUrl + ' error:' + errorThrown);
                }
            })
        })
    }

    private areAllChartsLoaded(): boolean {
        return this.state.charts.findIndex((chart) => 
            chart.intervals === null
        ) === -1;
    }
}

export default RoutesCharts;