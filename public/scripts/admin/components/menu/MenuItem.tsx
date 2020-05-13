import React from 'react'
import { isNil } from 'lodash'
import classNames from 'classnames'
import $ from 'jquery'

export interface ItemProps {
    name: string,
    link?: string,
    icon: string,
    submenu?: ItemProps[],
    _collapsed: boolean,
    _id: string,
    _onToggle: (id: string, collapsed: boolean) => void
}

class MenuItem extends React.Component<ItemProps> {
    private submenuRef = React.createRef<HTMLUListElement>();
    private triangleRef = React.createRef<HTMLElement>();
    private lastSubmenuHeight = 0;

    public constructor(props: ItemProps) {
        super(props);

        this.toggleCollapse = this.toggleCollapse.bind(this);
    }

    public componentDidMount(): void {
        this.initCollapse();
    }

    public render(): React.ReactNode {
        const hasSubmenu = !isNil(this.props.submenu) && this.props.submenu.length;
        const linkClasses = classNames(
            'menu__link',
            {'menu__link--parent': hasSubmenu}
        );
        const link = !hasSubmenu ? this.props.link : null;
        const iconClass = `menu__icon fontello icon-${this.props.icon}`;
        
        return (
            <li className="menu__item">
                <a 
                    className={linkClasses} 
                    href={link}
                    onClick={hasSubmenu ? this.toggleCollapse : null}
                >
                    <i className={iconClass}></i>
                    <span className="menu__label">{this.props.name}</span>
                    {hasSubmenu &&
                        <i 
                            className="menu__arrow fontello icon-down-dir"
                            ref={this.triangleRef}
                        ></i>
                    }
                </a>
                {hasSubmenu &&
                    <ul className="menu__submenu" ref={this.submenuRef}>
                        {this.props.submenu.map((subitem, index) =>
                            <MenuItem
                                key={index}
                                icon={subitem.icon}
                                name={subitem.name}
                                link={subitem.link}
                                submenu={subitem.submenu}
                                _collapsed={subitem._collapsed}
                                _id={`${this.props._id}-${index}`}
                                _onToggle={this.props._onToggle}
                            ></MenuItem>
                        )}
                    </ul>
                }
            </li>
        )
    }

    private initCollapse() {

        if (!this.submenuRef || !this.props._collapsed) return;
        this.lastSubmenuHeight = $(this.submenuRef.current).height();
        $(this.submenuRef.current).height(0);
        $(this.triangleRef.current).css('transform', 'rotate(-90deg)');
    }

    public toggleCollapse(event: React.MouseEvent) {
        if (!this.submenuRef) return;
        if ($(this.submenuRef.current).height()) {
            this.collapse();
            this.props._onToggle(this.props._id, true);
        } else {
            this.expand();
            this.props._onToggle(this.props._id, false);
        }
        event.preventDefault();
        event.stopPropagation();
    }

    private collapse() {
        this.lastSubmenuHeight = $(this.submenuRef.current).height();
        $(this.submenuRef.current).animate({ 'height': 0 }, 250);

        $({ deg: 0 }).animate({ deg: -90 }, {
            duration: 250,
            step: (now) => {
                $(this.triangleRef.current).css({
                    transform: 'rotate(' + now + 'deg)'
                });
            }
        });
    }

    private expand() {
        $(this.submenuRef.current).animate({
            'height': this.lastSubmenuHeight
        }, 250, function() { $(this).css({'height': 'auto'}) });

        $({ deg: -90 }).animate({ deg: 0 }, {
            duration: 250,
            step: (now) => {
                $(this.triangleRef.current).css({
                    transform: 'rotate(' + now + 'deg)'
                });
            }
        });
    }
}

export default MenuItem;