import React from 'react'
import classNames from 'classnames'
import TableDetails, { DetailsProps } from './TableDetails';
import { isNil } from 'lodash';

export interface TableItemData {
    cells: any[],
    details?: DetailsProps[],
    detailsIndent?: boolean
}

interface Props {
    item: TableItemData,
    collapsable?: boolean
}

interface State {
    collapsed: boolean
}

class TableItem extends React.Component<Props, State> {
    public constructor(props: Props) {
        super(props);

        this.state = {
            collapsed: this.props.collapsable ? true : false
        }

        this.toggleCollapse = this.toggleCollapse.bind(this);
    }

    public render(): React.ReactNode {
        return <tbody className={classNames(
            'table__item-wrapper',
            {'table__item-wrapper--opened': 
                this.props.collapsable && !this.state.collapsed}
        )}>
            <tr className="table__item">
                {this.props.collapsable &&
                    <td 
                        className="table__cell table__cell--collapse"
                        onClick={this.toggleCollapse}
                    >
                        <i className={classNames(
                            'table__collapse',
                            {'icon-down-open': this.state.collapsed},
                            {'icon-up-open': !this.state.collapsed}
                        )}></i>
                    </td>
                }
                {this.props.item.cells.map((value, index) =>
                    <td key={index} className="table__cell">
                        {value}
                    </td>
                )}
            </tr>
            {this.props.item.details && !this.state.collapsed &&
                <tr className="table__details-wrapper">
                    <td className={classNames(
                        "table__details",
                        {'table__details--indent':
                            !isNil(this.props.item.detailsIndent)
                                ? this.props.item.detailsIndent
                                : true
                        }
                    )} colSpan={100}>
                        <div className="table__details-content">
                            {this.props.item.details.map((details, index) =>
                                <TableDetails
                                    key={index}
                                    title={details.title}
                                    content={details.content}
                                />
                            )}
                        </div>
                    </td>
                </tr>
            }
        </tbody>
    }

    private toggleCollapse(event: React.MouseEvent): void {
        this.setState((state) => ({ collapsed: !state.collapsed }));
        event.preventDefault();
        event.stopPropagation();
    }
}

export default TableItem;