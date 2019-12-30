import React from 'react'
import $ from 'jquery'

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

class Item extends React.Component<Props> {
    private detailsContentRef = React.createRef<HTMLDivElement>();
    private lastDetailsHeight = 0;

    public constructor(props: Props) {
        super(props);
        this.toggleCollapse = this.toggleCollapse.bind(this);
    }

    public componentDidMount(): void {
        if (this.props.collapsable) this.collapse();
    }

    public render(): React.ReactNode {
        return <tbody 
            className="table__item-wrapper" 
            onClick={this.toggleCollapse}
        >
            <tr className="table__item">
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
                            ref={this.detailsContentRef}
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
        if (!this.props.collapsable) return;
        if (this.isCollapsed()) this.expand();
        else this.collapse();
        event.stopPropagation();
        event.preventDefault();
    }

    private collapse(): void {
        if (this.isCollapsed()) return;
        const detailsContent = this.detailsContentRef.current;
        this.lastDetailsHeight = $(detailsContent).height();
        $(detailsContent).height(0);
    }

    private expand(): void {
        if (!this.isCollapsed()) return;
        if (!this.props.item.details) return;
        $(this.detailsContentRef.current).css({ 'height': 'auto' });
    }

    private isCollapsed(): boolean {
        if (!this.props.item.details) return true;
        const detailsContent = this.detailsContentRef.current;
        return $(detailsContent).height() ? false : true;
    }
}

export default Item;