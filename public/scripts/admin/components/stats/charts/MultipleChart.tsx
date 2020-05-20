import React from 'react';
import {
    AreaChart, Area, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import Table, { SortOrder } from '../../table/Table';
import { round } from 'lodash';
import MultipleChartSettings, { MultipleChartSettingsData } from './MultipleChartSettings';
import { ChartProps, SecondInterval, SortColumn } from '../_common';
import ContentHeader from '../../content/ContentHeader';
import Breadcrumbs from '../../common/Breadcrumbs';
import $ from 'jquery';

interface MultipleStatsAPIResult {
    [object: string]: [{
        value?: number,
        time: string,
        timestamp: number
    }]
}

interface TimeIntervalValues {
    time: string,
    // Значения в values, могут быть null. Тогда они не будут учитываться при
    // подсчете среднего значения. Во всех других случаях они преобразуются в 0. 
    values: { [objectName: string]: any }
}

interface MultipleChartState {
    intervals: TimeIntervalValues[],
    limit: number,
    sortField: SortColumn,
    sortOrder: SortOrder,
    secondInterval: SecondInterval
    intervalCount: number,
}

class MultipleChart extends React.Component<ChartProps, MultipleChartState> {
    private lineColors = [
        '#3F51B5', // blue
        '#4CAF50', // green
        '#F44336', // red
        '#b3ab00', // yellow
        '#9c27b0' // purple
    ];
    
    public constructor(props: ChartProps) {
        super(props);
        this.state = {
            intervals: null,
            secondInterval: SecondInterval.DAY,
            intervalCount: 10,
            limit: 5,
            sortField: SortColumn.AVG,
            sortOrder: SortOrder.DESC,
        }

        this.onMultipleChartUpdate = this.onMultipleChartUpdate.bind(this);
    }

    public componentDidMount() {
        this.loadChartData({
            sortField: this.state.sortField,
            sortOrder: this.state.sortOrder,
            secondInterval: this.state.secondInterval
        }, this.props.onInitLoad);
    }

    public render(): React.ReactNode {
        const props = this.props;
        const state = this.state;
        if (!props.isReady) return <></>;
        return <>
            <ContentHeader>
                <Breadcrumbs items={[...props.basePaths, { 'name': props.title }]} />
            </ContentHeader>
            <div className="box chart">
                <ResponsiveContainer height={200} width="99%">
                    <AreaChart
                        data={state.intervals.map((interval) => {
                            return {
                                time: interval.time,
                                // Нужно заменить все null в значениях на 0,
                                // потому что библиотека графиков сама этого
                                // не делает.
                                values: (() => {
                                    const result = {};
                                    for (const name in interval.values) {
                                        if (interval.values.hasOwnProperty(name)) {
                                            const value = interval.values[name];
                                            result[name.split('.').join('&point;')] = value !== null ? value : 0;
                                        }
                                    }
                                    return result;
                                })()
                            }
                        })}
                        margin={{top: 10, right: 30, left: -20, bottom: 10}}
                    >
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="time" />
                        <YAxis />
                        <Tooltip isAnimationActive={false} />
                        {state.intervals.length && Object
                            .keys(state.intervals[0].values)
                            .map((name: string, index: number) => (
                                <Area
                                    key={index}
                                    type="monotone"
                                    name={name.split('&point;').join('.')}
                                    dataKey={`values[${name.split('.').join('&point;')}]`}
                                    fill={this.getColorByNumber(index)}
                                    stroke={this.getColorByNumber(index)}
                                    fillOpacity={0.05}
                                    dot={{ r: 3 }}
                                    activeDot={{ r: 4 }}
                                />
                            ))
                        }
                    </AreaChart>
                </ResponsiveContainer>
                <Table 
                    className="chart-table"
                    headers={[
                        'Route',
                        <span>{state.sortField === SortColumn.AVG ? 'Avg' : 'Max'}</span>
                    ]}
                    items={(state.intervals.length ? Object.keys(state.intervals[0].values) : [])
                        .map((name: string, index: number) => {
                            return {
                                cells: [
                                    <>
                                        <i 
                                            className="icon-bookmark chart-table__color-icon"
                                            style={{color: this.getColorByNumber(index)}}
                                        ></i>
                                        {name}
                                    </>,
                                    state.sortField === SortColumn.AVG 
                                        ? this.findAverageOf(name)
                                        : this.findMaxOf(name)
                                ]
                            }
                        })
                        .sort((a, b) => {
                            const sortColumnIndex = 1;
                            const aValue = a.cells[sortColumnIndex];
                            const bValue = b.cells[sortColumnIndex];
                            return -(aValue > bValue
                                ? 1 : (aValue == bValue ? 0 : -1));
                        })
                    }
                />
                <div className="box__details">
                    <span className="box__header">Настройки</span>
                    <MultipleChartSettings onUpdate={this.onMultipleChartUpdate} />
                </div>
            </div>
        </>
    }

    private loadChartData(
        settings: MultipleChartSettingsData,
        setFinished?: () => void
    ) {
        this.loadMultipleStats(settings).then((result) => {
            this.setState({
                intervals: result,
                sortField: settings.sortField,
                sortOrder: settings.sortOrder,
                secondInterval: settings.secondInterval
            });
            setFinished && setFinished();
        });
    }

    private loadMultipleStats(
        settings: MultipleChartSettingsData
    ): Promise<TimeIntervalValues[]> {
        return new Promise<TimeIntervalValues[]>((resolve, reject) => {
            $.ajax({
                url: this.props.apiUrl,
                method: 'get',
                data: {
                    limit: this.state.limit,
                    field: settings.sortField,
                    order: settings.sortOrder,
                    intervals: this.state.intervalCount,
                    sec_interval: settings.secondInterval
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
                    reject(this.props.apiUrl + ' error:' + errorThrown);
                }
            })
        })
    }

    private getColorByNumber(number: number): string {
        return this.lineColors[number % this.lineColors.length];
    }

    private findMaxOf(name: string): number {
        let max = Number.MIN_SAFE_INTEGER;
        for (let i = 0; i < this.state.intervals.length; i++) {
            const values = this.state.intervals[i];
            let currentValue = values.values[name];
            if (currentValue === null) currentValue = 0;
            if (currentValue > max) max = currentValue;
        }
        return max;
    }

    private findAverageOf(name: string): number {
        let average = 0;
        let count = 0;
        for (let i = 0; i < this.state.intervals.length; i++) {
            const values = this.state.intervals[i];
            const currentValue = values.values[name];
            if (currentValue === null) continue;
            average += currentValue;
            count += 1;
        }
        return round(average / count, 3);
    }

    private onMultipleChartUpdate(
        newSettings: MultipleChartSettingsData,
        setFinished: () => void
    ) {
        this.loadChartData(newSettings, setFinished);
    }
}

export default MultipleChart;