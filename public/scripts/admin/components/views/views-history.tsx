import React from 'react';
import Breadcrumbs from '../common/Breadcrumbs';
import Table from '../table';
import RouteRequest from '../routes/Request';
import { isNil } from 'lodash';
import Status, { Type } from '../status';

interface ViewStat {
    id: number,
    class: string,
    name: string,
    file: string,
    parentId?: number,
    durationSec: number
}

interface ViewRoute {
    route: string
    views: ViewStat[]
}

interface ViewsHistoryProps {
    routes: ViewRoute[],
    clearStatsUrl: string
}

class ViewsHistory extends React.Component<ViewsHistoryProps> {
    public render(): React.ReactNode {
        return (
            <>
                <div className="content__header">
                    <Breadcrumbs items={[{
                        name: 'Мониторинг'
                    }, {
                        name: 'Виды'
                    }]} />
                    <a href={this.props.clearStatsUrl} className="button">
                        Очистить статистику
                    </a>
                </div>
                <div className="box box--table">
                    <Table 
                        className="routes"
                        headers={['Route', 'Views']}
                        items={this.props.routes.map((route) => ({
                            cells: [
                                <RouteRequest route={route.route} />,
                                route.views.length
                            ],
                            details: (() => {
                                if (!route.views.length) return;
                                return [{
                                    content: (
                                        <Table
                                            headers={[
                                                'Class',
                                                'Name',
                                                'Load'
                                            ]}
                                            items={route.views.map((view) => ({
                                                cells: [
                                                    view.class,
                                                    view.name,
                                                    <span 
                                                        className="routes__duration"
                                                    >
                                                        {view.durationSec} sec
                                                    </span>
                                                ],
                                                details: [{
                                                    title: 'File',
                                                    content: view.file
                                                }, {
                                                    title: 'Parent',
                                                    content: !isNil(view.parentId)
                                                        ? route.views.find((stat) =>
                                                            stat.id === view.parentId
                                                        ).file
                                                        : (
                                                            <Status
                                                                type={Type.EMPTY}
                                                                message="No parent"
                                                            />
                                                        )
                                                }]
                                            }))}
                                        />
                                    )
                                }]
                            })()
                        }))}
                    />
                </div>
            </>
        )
    }
}

export default ViewsHistory;