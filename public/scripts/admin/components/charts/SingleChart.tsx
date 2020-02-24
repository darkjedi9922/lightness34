import React from 'react';
import {
    AreaChart, Area, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import Table from '../table/table';
import { round, maxBy, meanBy, sumBy } from 'lodash';

export interface TimeIntervalValue {
    time: string,
    value: number
}

export interface SingleChartProps {
    intervals: TimeIntervalValue[]
}

class SingleChart extends React.Component<SingleChartProps> {
    private lineColor = '#3F51B5'; // blue

    public render(): React.ReactNode {
        return <>
            <div className="box chart">
                <ResponsiveContainer height={200} width="99%">
                    <AreaChart
                        data={this.props.intervals}
                        margin={{top: 10, right: 30, left: -20, bottom: 10}}
                    >
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="time" />
                        <YAxis />
                        <Tooltip isAnimationActive={false} />
                        <Area
                            type="monotone"
                            dataKey="value"
                            fill={this.lineColor}
                            stroke={this.lineColor}
                            fillOpacity={0.05}
                            dot={{ r: 3 }}
                            activeDot={{ r: 4 }}
                        />
                    </AreaChart>
                </ResponsiveContainer>
                <Table 
                    className="chart-table"
                    headers={['Summary count', 'Max', 'Avg']}
                    items={[{
                        cells: [
                            sumBy(this.props.intervals, 'value'),
                            maxBy(this.props.intervals, 'value').value,
                            round(meanBy(this.props.intervals, 'value'), 3)
                        ]
                    }]}
                />
            </div>
        </>
    }
}

export default SingleChart;