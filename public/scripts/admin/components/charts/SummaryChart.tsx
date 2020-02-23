import React from 'react';
import {
    AreaChart, Area, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import Table from '../table/table';

export interface TimeIntervalValues {
    time: string,
    // Значения в values, могут быть null. Тогда они не будут учитываться при
    // подсчете среднего значения. Во всех других случаях они преобразуются в 0. 
    values: { [objectName: string]: any }
}

export interface SummaryChartProps {
    intervals: TimeIntervalValues[]
}

class SummaryChart extends React.Component<SummaryChartProps> {
    private lineColors = [
        '#3F51B5', // blue
        '#4CAF50', // green
        '#F44336', // red
        '#b3ab00', // yellow
        '#9c27b0' // purple
    ];

    public render(): React.ReactNode {
        return <>
            <div className="box chart">
                <ResponsiveContainer height={250} width="99%">
                    <AreaChart
                        data={this.props.intervals.map((interval) => {
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
                                            result[name] = value !== null ?
                                                value : 0;
                                        }
                                    }
                                    return result;
                                })()
                            }
                        })}
                        margin={{
                            top: 10, right: 30, left: -20, bottom: 10,
                        }}
                    >
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="time" />
                        <YAxis />
                        <Tooltip isAnimationActive={false} />
                        {this.props.intervals.length && Object
                            .keys(this.props.intervals[0].values)
                            .map((name: string, index: number) => (
                                <Area
                                    key={index}
                                    type="monotone"
                                    name={name}
                                    dataKey={`values[${name}]`}
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
                    headers={['Route', 'Max', 'Avg']}
                    items={(this.props.intervals.length ?
                            Object.keys(this.props.intervals[0].values) : []
                        ).map((name: string, index: number) => {
                            return {
                                cells: [
                                    <>
                                        <i className="
                                            icon-bookmark 
                                            chart-table__color-icon
                                        " style={{
                                            color: this.getColorByNumber(index)
                                        }}></i>
                                        {name}
                                    </>,
                                    this.findMaxOf(name),
                                    this.findAverageOf(name)
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
            </div>
        </>
    }

    private getColorByNumber(number: number): string {
        return this.lineColors[number % this.lineColors.length];
    }

    private findMaxOf(name: string): number {
        let max = Number.MIN_SAFE_INTEGER;
        for (let i = 0; i < this.props.intervals.length; i++) {
            const values = this.props.intervals[i];
            let currentValue = values.values[name];
            if (currentValue === null) currentValue = 0;
            if (currentValue > max) max = currentValue;
        }
        return max;
    }

    private findAverageOf(name: string): number {
        let average = 0;
        let count = 0;
        // 10 в степени n, где округление числа до n-ой точки, после запятой.
        const roundTen = Math.pow(10, 3);
        for (let i = 0; i < this.props.intervals.length; i++) {
            const values = this.props.intervals[i];
            const currentValue = values.values[name];
            if (currentValue === null) continue;
            average += currentValue;
            count += 1;
        }
        return Math.round((average / count) * roundTen) / roundTen;
    }
}

export default SummaryChart;