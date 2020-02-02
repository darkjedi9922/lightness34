import React from 'react';
import Breadcrumbs from '../../common/Breadcrumbs';
import {
    LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import $ from 'jquery';
import { isNil } from 'lodash';

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
        return (<>
            <div className="content__header">
                <div className="breadcrumbs-wrapper">
                    <Breadcrumbs items={[{
                        name: 'Мониторинг'
                    }, {
                        name: 'База данных'
                    }, {
                        name: 'Запросы'
                    }]} />
                    {isNil(this.state.data) &&
                        <i className="icon-spin1 animate-spin content__loading"></i>
                    }
                </div>
            </div>
            {!isNil(this.state.data) &&
                <div className="box chart">
                    <ResponsiveContainer height={400} width="99%">
                        <LineChart
                            data={this.state.data}
                            margin={{
                                top: 10, right: 30, left: -20, bottom: 10,
                            }}
                        >
                            <XAxis dataKey="time"/>
                            <YAxis />
                            <Tooltip isAnimationActive={false} />
                            <Line 
                                type="monotone"
                                dataKey="count" 
                                className="chart__line"
                                dot={{ r: 4, className: 'chart__dot' }}
                                activeDot={{ r: 5, className: 'chart__dot' }}
                            />
                        </LineChart>
                    </ResponsiveContainer>
                </div>
            }
        </>);
    }
}

export default QueriesPage;