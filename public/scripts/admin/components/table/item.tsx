import React from 'react'
import classNames from 'classnames'

export interface ItemDetails {
    title?: string,
    content: any
}

export interface TableItem {
    cells: any[]
    details?: ItemDetails[]
}

interface Props {
    item: TableItem,
    collapsable?: boolean
}

interface State {
    collapsed: boolean
}

class Item extends React.Component<Props, State> {
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
            {'table__item-wrapper--opened': !this.state.collapsed}
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
            {this.props.item.details &&
                <tr className="table__details-wrapper">
                    <td className="table__details" colSpan={100}>
                        <div
                            className="table__details-content" 
                            style={{
                                // Если использовать переключение display, то колонки
                                // таблицы почему-то начинают менять размер.
                                height: this.state.collapsed ? 0 : 'auto'
                            }}
                        >
                            {this.props.item.details.map((details, index) =>
                                <div key={index} className="details">
                                    {details.title &&
                                        <span className="details__header">
                                            {details.title}
                                        </span>
                                    }
                                    <div className="details__content">
                                        {details.content}
                                    </div>
                                </div>
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

export default Item;