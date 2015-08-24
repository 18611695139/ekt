/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'textfield', function( editor ) {
	var autoAttributes = { value:1,size:1,maxLength:1 };

	var acceptedTypes = { text:1,password:1 };

	function autoCommit( data ) {
		var element = data.element;
		var value = this.getValue();

		value ? element.setAttribute( this.id, value ) : element.removeAttribute( this.id );
	}

	function autoSetup( element ) {
		var value = element.hasAttribute( this.id ) && element.getAttribute( this.id );
		this.setValue( value || '' );
	}

	return {
		title: editor.lang.forms.textfield.title,
		minWidth: 400,
		minHeight: 200,
		onShow: function() {
			delete this.textField;

			var element = this.getParentEditor().getSelection().getSelectedElement();
			if ( element && element.getName() == "input" && ( acceptedTypes[ element.getAttribute( 'type' ) ] || !element.getAttribute( 'type' ) ) ) {
				this.textField = element;
				this.setupContent( element );
			}
		},
		onOk: function() {
			var editor = this.getParentEditor(),
				element = this.textField,
				isInsertMode = !element;

			if ( isInsertMode ) {
				element = editor.document.createElement( 'input' );
				element.setAttribute( 'type', 'text' );
			}

			var data = { element: element };

			if ( isInsertMode )
				editor.insertElement( data.element );

			this.commitContent( data );

			// Element might be replaced by commitment.
			if ( !isInsertMode )
				editor.getSelection().selectElement( data.element );
		},
		onLoad: function() {            
			this.foreach( function( contentObj ) {
				if ( contentObj.getValue ) {
					if ( !contentObj.setup )
						contentObj.setup = autoSetup;
					if ( !contentObj.commit )
						contentObj.commit = autoCommit;
				}
			});
		},
		contents: [
			{
			id: 'info',
			label: editor.lang.forms.textfield.title,
			title: editor.lang.forms.textfield.title,
			elements: [
				{
				type: 'hbox',
				widths: [ '50%', '50%' ],
				children: [
					{
					id: '_cke_saved_name',
					type: 'text',
					label: editor.lang.forms.textfield.name,
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
					commit: function( data ) {
						var element = data.element;

						if ( this.getValue() )
							element.data( 'cke-saved-name', this.getValue() );
						else {
							element.data( 'cke-saved-name', false );
							element.removeAttribute( 'name' );
						}
					}
				},
					{
					id: 'label',
					type: 'text',
					label: '标签',
					'default': '',
					accessKey: 'V',
                    validate: function(){
                        if(!this.getValue()){
                            alert('标签不能为空');
                            return false;
                        }
                    },
					setup: function( element ) {
						this.setValue( element.data( 'cke-saved-label' ) || element.getAttribute( 'label' ) || '' );
					},
					commit: function( data ) {
						var element = data.element;

						if ( this.getValue() )
							element.data( 'cke-saved-label', this.getValue() );
						else {
							element.data( 'cke-saved-label', false );
							element.removeAttribute( 'label' );
						}
					}
				}
				]
			},
                {
					id: 'value',
					type: 'text',
					label: editor.lang.forms.textfield.value,
					'default': '',
					accessKey: 'V',
					commit: function( data ) {
						if ( CKEDITOR.env.ie && !this.getValue() ) {
							var element = data.element,
								fresh = new CKEDITOR.dom.element( 'input', editor.document );
							element.copyAttributes( fresh, { value:1 } );
							fresh.replace( element );
							data.element = fresh;
						} else
							autoCommit.call( this, data );
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
    					commit: function( data ) {
    						var element = data.element;
    
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
    					commit: function( data ) {
    						var element = data.element;
    
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
            				this.setValue( element.hasAttribute( 'key' ) );
            			},
    					commit: function( data ) {
    						var element = data.element;
    
    						if ( this.getValue() )
    							element.data( 'cke-saved-key', this.getValue() );
    						else {
    							element.data( 'cke-saved-key', false );
    							element.removeAttribute( 'key' );
    						}
    					}
    				},
    				{
    				type: 'hbox',
    				widths: [ '50%', '50%' ],
    				children: [
    					{
    					id: 'size',
    					type: 'text',
    					label: editor.lang.forms.textfield.charWidth,
    					'default': '',
    					accessKey: 'C',
    					style: 'width:50px',
    					validate: CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed )
    				},
    					{
    					id: 'maxLength',
    					type: 'text',
    					label: editor.lang.forms.textfield.maxChars,
    					'default': '',
    					accessKey: 'M',
    					style: 'width:50px',
    					validate: CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed )
    				}
    				],
    				onLoad: function() {
    					// Repaint the style for IE7 (#6068)
    					if ( CKEDITOR.env.ie7Compat )
    						this.getElement().setStyle( 'zoom', '100%' );
    				}
    			},
                {
                type: 'hbox',
				widths: [ '50%', '50%' ],
                children: [
                    {
    				id: 'type',
    				type: 'select',
    				label: editor.lang.forms.textfield.type,
    				'default': 'text',
    				accessKey: 'M',
    				items: [
    					[ editor.lang.forms.textfield.typeText, 'text' ],
    					[ editor.lang.forms.textfield.typePass, 'password' ]
    					],
    				setup: function( element ) {
    					this.setValue( element.getAttribute( 'type' ) );
    				},
    				commit: function( data ) {
    					var element = data.element;
    
    					if ( CKEDITOR.env.ie ) {
    						var elementType = element.getAttribute( 'type' );
    						var myType = this.getValue();
    
    						if ( elementType != myType ) {
    							var replace = CKEDITOR.dom.element.createFromHtml( '<input type="' + myType + '"></input>', editor.document );
    							element.copyAttributes( replace, { type:1 } );
    							replace.replace( element );
    							data.element = replace;
    						}
    					} else
    						element.setAttribute( 'type', this.getValue() );
    				}
    			},
                    {
    				id: 'texttype',
    				type: 'select',
    				label: editor.lang.forms.textfield.type,
    				'default': 'text',
    				accessKey: 'M',
    				items: [
    					[ '文本', 'text' ],
    					[ '数字', 'number' ],
                        [ '日期', 'date' ],
                        [ '时间', 'time' ],
                        [ '日期时间', 'datetime' ]
    					],
    				setup: function( element ) {
    					this.setValue( element.getAttribute( 'texttype' ) );
    				},
    				commit: function( data ) {
    					var element = data.element;
    						element.setAttribute( 'texttype', this.getValue() );
    				}
                }
                ]
            }
			]
		}
		]
	};
});
