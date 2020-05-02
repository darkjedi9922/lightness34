import React from 'react';
import ContentHeader  from '../content-header';
import LoadingContent from '../loading-content';
import Breadcrumbs from '../common/Breadcrumbs';
import $ from 'jquery';
import MultipleChart, { TimeIntervalValues, SortColumn } from './charts/MultipleChart';
import SingleChart, { TimeIntervalValue } from './charts/SingleChart';
import { SortOrder } from '../table/table';
import { MultipleChartSettingsData } from './charts/MultipleChartSettings';

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

interface ChartsProps {
    name: string,
    stat: string
}

interface ChartsState {
    charts: (SingleChartData|MultipleChartData)[]
}

interface CountsAPIResultItem extends TimeIntervalValue {}

interface MultipleStatsAPIResult {
    [object: string]: [{
        value?: number,
        time: string,
        timestamp: number
    }]
}

class StatCharts extends React.Component<ChartsProps, ChartsState> {
    public constructor(props: ChartsProps) {
        super(props);
        this.state = {
            charts: [{
                title: 'Общее количество',
                type: 'single',
                apiUrl: `/api/stats/${props.stat}/summary`,
                intervals: null,
                secondInterval: SecondInterval.DAY,
                intervalCount: 10
            } as SingleChartData, {
                title: 'Макс. количество',
                type: 'multiple',
                apiUrl: `/api/stats/${props.stat}/count`,
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
                apiUrl: `/api/stats/${props.stat}/durations`,
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
            { 'name': this.props.name },
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
                        onUpdate={(newSettings, setFinished) => {
                            this.onMultipleChartUpdate(index, newSettings, setFinished)
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

    private loadChartData(chart: SingleChartData|MultipleChartData, setFinished?: () => void) {
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
        promise.then((result) => {
            this.updateChartIntervals(chartIndex, result);
            setFinished && setFinished();
        });
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
                success: (result: CountsAPIResultItem[]) => {
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
                success: (result: MultipleStatsAPIResult) => {
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

    private onMultipleChartUpdate(
        chartIndexInState: number,
        newSettings: MultipleChartSettingsData,
        setFinished: () => void
    ) {
        const chart = this.state.charts[chartIndexInState] as MultipleChartData;
        chart.sortField = newSettings.sortField;
        chart.sortOrder = newSettings.sortOrder;
        chart.secondInterval = newSettings.secondInterval;
        this.loadChartData(chart, setFinished);
    }
}

export default StatCharts;