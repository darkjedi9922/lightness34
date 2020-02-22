import React from 'react';
import {
    AreaChart, Area, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import Table from '../table/table';

export interface SummaryData {
    max: number,
    avg: number
}

export interface TimeIntervalValues {
    time: string,
    values: { [objectName: string]: any }
}

export interface SummaryChartProps {
    objects: { [name: string]: SummaryData }
    values: TimeIntervalValues[]
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
                        data={this.props.values}
                        margin={{
                            top: 10, right: 30, left: -20, bottom: 10,
                        }}
                    >
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="time" />
                        <YAxis />
                        <Tooltip isAnimationActive={false} />
                        {Object.keys(this.props.objects)
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
                        ))}
                    </AreaChart>
                </ResponsiveContainer>
                <Table 
                    className="chart-table"
                    headers={['Route', 'Max', 'Avg']}
                    items={Object.keys(this.props.objects)
                        .map((url: string, index: number) => {
                            return {
                                cells: [
                                    <>
                                        <i className="
                                            icon-bookmark 
                                            chart-table__color-icon
                                        " style={{
                                            color: this.getColorByNumber(index)
                                        }}></i>
                                        {url}
                                    </>,
                                    this.props.objects[url].max,
                                    this.props.objects[url].avg
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
}

export default SummaryChart;