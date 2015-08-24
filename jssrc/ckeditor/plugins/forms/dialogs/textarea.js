/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'textarea', function( editor ) {
	return {
		title: editor.lang.forms.textarea.title,
		minWidth: 350,
		minHeight: 220,
		onShow: function() {
			delete this.textarea;

			var element = this.getParentEditor().getSelection().getSelectedElement();
			if ( element && element.getName() == "textarea" ) {
				this.textarea = element;
				this.setupContent( element );
			}
		},
		onOk: function() {
			var editor,
				element = this.textarea,
				isInsertMode = !element;

			if ( isInsertMode ) {
				editor = this.getParentEditor();
				element = editor.document.createElement( 'textarea' );
			}
			this.commitContent( element );

			if ( isInsertMode )
				editor.insertElement( element );
		},
		contents: [
			{
			id: 'info',
			label: editor.lang.forms.textarea.title,
			title: editor.lang.forms.textarea.title,
			elements: [
				{
				id: '_cke_saved_name',
				type: 'text',
				label: editor.lang.common.name,
				'default': '',
				accessKey: 'N',
                validate: function(){
                    if(!this.getValue()){
                        alert( '名称不能为空' );
                        return false;
                    }
                    else if(!/^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){0,19}$/.exec(this.getValue())){
                        alert("名称只能是以字母开头的含数字和字母及“_”的长度为20个字符以内的字符串");
                        return false;
                    }
                },
				setup: function( element ) {
					this.setValue( element.data( 'cke-saved-name' ) || element.getAttribute( 'name' ) || '' );
				},
				commit: function( element ) {
					if ( this.getValue() )
						element.data( 'cke-saved-name', this.getValue() );
					else {
						element.data( 'cke-saved-name', false );
						element.removeAttribute( 'name' );
					}
				}
			},
                {
				id: '_cke_saved_label',
				type: 'text',
				label: '标签',
				'default': '',
				accessKey: 'N',
                validate: function(){
                    if(!this.getValue()){
                        alert('标签不能为空');
                        return false;
                    }
                },
				setup: function( element ) {
					this.setValue( element.data( 'cke-saved-label' ) || element.getAttribute( 'label' ) || '' );
				},
				commit: function( element ) {
					if ( this.getValue() )
						element.data( 'cke-saved-label', this.getValue() );
					else {
						element.data( 'cke-saved-label', false );
						element.removeAttribute( 'label' );
					}
				}
			},
				{
				type: 'hbox',
				widths: [ '50%', '50%' ],
				children: [
					{
					id: 'cols',
					type: 'text',
					label: editor.lang.forms.textarea.cols,
					'default': '',
					accessKey: 'C',
					style: 'width:50px',
					validate: CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
					setup: function( element ) {
						var value = element.hasAttribute( 'cols' ) && element.getAttribute( 'cols' );
						this.setValue( value || '' );
					},
					commit: function( element ) {
						if ( this.getValue() )
							element.setAttribute( 'cols', this.getValue() );
						else
							element.removeAttribute( 'cols' );
					}
				},
					{
					id: 'rows',
					type: 'text',
					label: editor.lang.forms.textarea.rows,
					'default': '',
					accessKey: 'R',
					style: 'width:50px',
					validate: CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
					setup: function( element ) {
						var value = element.hasAttribute( 'rows' ) && element.getAttribute( 'rows' );
						this.setValue( value || '' );
					},
					commit: function( element ) {
						if ( this.getValue() )
							element.setAttribute( 'rows', this.getValue() );
						else
							element.removeAttribute( 'rows' );
					}
				}
				]
			},
				{
				id: 'value',
				type: 'textarea',
				label: editor.lang.forms.textfield.value,
				'default': '',
				setup: function( element ) {
					this.setValue( element.$.defaultValue );
				},
				commit: function( element ) {
					element.$.value = element.$.defaultValue = this.getValue();
				}
			},
                {
				type: 'hbox',
				widths: [ '50%', '50%' ],
				children: [
                {
					id: 'readonly',
        			type: 'checkbox',
        			label: '是否只读',
        			'default': '',
        			accessKey: 'S',
        			value: "checked",
        			setup: function( element ) {
        				this.setValue( element.hasAttribute( 'readonly' ) );
        			},
					commit: function( element ) {
						if ( this.getValue() )
							element.data( 'cke-saved-readonly', this.getValue() );
						else {
							element.data( 'cke-saved-readonly', false );
							element.removeAttribute( 'readonly' );
						}
					}
				},
					{
					id: 'required',
        			type: 'checkbox',
        			label: '是否必填',
        			'default': '',
        			accessKey: 'S',
        			setup: function( element ) {
        				this.setValue( element.hasAttribute( 'required' ) );
        			},
					commit: function( element ) {
						if ( this.getValue() )
							element.data( 'cke-saved-required', this.getValue() );
						else {
							element.data( 'cke-saved-required', false );
							element.removeAttribute( 'required' );
						}
					}
				}
				]
		    },
                {
				id: 'key',
    			type: 'checkbox',
    			label: '是否关键字段（关键字段用于提交表单时记录日志的字段）',
    			'default': '',
    			accessKey: 'S',
    			setup: function( element ) {
    				this.setValue( element.getAttribute( 'key' ) );
    			},
				commit: function( element ) {
					if ( this.getValue() )
						element.data( 'cke-saved-key', this.getValue() );
					else {
						element.data( 'cke-saved-key', false );
						element.removeAttribute( 'key' );
					}
				}
			}
			]
		}
		]
	};
});
