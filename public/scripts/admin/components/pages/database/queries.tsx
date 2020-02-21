import React from 'react';
import Breadcrumbs from '../../common/Breadcrumbs';
import {
    AreaChart, Area, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import $ from 'jquery';
import { isNil } from 'lodash';
import LoadingContent from '../../loading-content';

interface QueriesCount {
    time: string,
    count: number
}

interface APIQueriesResult {
    data: QueriesCount[]
}

interface QueriesPageState {
    data?: QueriesCount[]
}

class QueriesPage extends React.Component<{}, QueriesPageState> {
    public constructor(props) {
        super(props);
        this.state = { data: null }
    }

    public componentDidMount(): void {
        $.ajax({
            url: '/api/stats/queries',
            dataType: 'json',
            success: (result: APIQueriesResult) => {
                this.setState(result);
            }
        })
    }

    public render(): React.ReactNode {
        return <>
            <div className="content__header">
                <div className="breadcrumbs-wrapper">
                    <Breadcrumbs items={[
                        { name: 'Мониторинг' },
                        { name: 'База данных' },
                        { name: 'Запросы' }
                    ]} />
                </div>
            </div>
            <LoadingContent>
                {!isNil(this.state.data) &&
                    <div className="box chart">
                        <ResponsiveContainer height={400} width="99%">
                            <AreaChart
                                data={this.state.data}
                                margin={{
                                    top: 10, right: 30, left: -20, bottom: 10,
                                }}
                            >
                                <XAxis dataKey="time" />
                                <YAxis />
                                <Tooltip isAnimationActive={false} />
                                <Area 
                                    type="monotone"
                                    dataKey="count" 
                                    className="chart__area"
                                    dot={{ r: 3, className: 'chart__dot' }}
                                    activeDot={{ r: 4, className: 'chart__dot' }}
                                />
                            </AreaChart>
                        </ResponsiveContainer>
                    </div>
                }
            </LoadingContent>
        </>;
    }
}

export default QueriesPage;