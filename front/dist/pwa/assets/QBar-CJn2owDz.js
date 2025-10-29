import{n as nt,p as be,bs as je,g as te,r as O,k as b,v as Pe,V as Ue,W as it,a4 as at,l as p,d as de,aB as rt,am as lt,bg as qe,J as ce,j as oe,ap as ye,P as Se,s as Y,a_ as st,aZ as ut,m as ne,ae as ct,y as dt,z as xe,A as ue,B as Ce,C as Qe,E as he,F as Be,I as ft,az as pt,bt as vt,bu as ht,L as $e,O as Ae,X as _t,G as De,H as mt,f as ke,bv as gt,U as bt,aA as yt}from"./index-BytFJZqo.js";import{Q as xt}from"./QResizeObserver-DTNlkK1v.js";import{r as Ct}from"./QSelect-BNkSTSfC.js";import{g as Ne,s as Re}from"./touch-BjYP5sR0.js";import{c as wt}from"./format-PQhwzCsV.js";import{a as Tt}from"./QBtnDropdown-BCEzdS7o.js";import{Q as St}from"./QTooltip-B-FvqnbI.js";import{c as Me,b as kt}from"./QMenu-B8wlO4BN.js";import{u as Pt,b as qt,c as $t}from"./QTable-BMAYUCL5.js";let At=0;const Lt=["click","keydown"],Et={icon:String,label:[Number,String],alert:[Boolean,String],alertIcon:String,name:{type:[Number,String],default:()=>`t_${At++}`},noCaps:Boolean,tabindex:[String,Number],disable:Boolean,contentClass:String,ripple:{type:[Boolean,Object],default:!0}};function It(e,t,o,a){const r=nt(je,be);if(r===be)return console.error("QTab/QRouteTab component needs to be child of QTabs"),be;const{proxy:n}=te(),s=O(null),_=O(null),w=O(null),$=b(()=>e.disable===!0||e.ripple===!1?!1:Object.assign({keyCodes:[13,32],early:!0},e.ripple===!0?{}:e.ripple)),c=b(()=>r.currentModel.value===e.name),q=b(()=>"q-tab relative-position self-stretch flex flex-center text-center"+(c.value===!0?" q-tab--active"+(r.tabProps.value.activeClass?" "+r.tabProps.value.activeClass:"")+(r.tabProps.value.activeColor?` text-${r.tabProps.value.activeColor}`:"")+(r.tabProps.value.activeBgColor?` bg-${r.tabProps.value.activeBgColor}`:""):" q-tab--inactive")+(e.icon&&e.label&&r.tabProps.value.inlineLabel===!1?" q-tab--full":"")+(e.noCaps===!0||r.tabProps.value.noCaps===!0?" q-tab--no-caps":"")+(e.disable===!0?" disabled":" q-focusable q-hoverable cursor-pointer")),d=b(()=>"q-tab__content self-stretch flex-center relative-position q-anchor--skip non-selectable "+(r.tabProps.value.inlineLabel===!0?"row no-wrap q-tab__content--inline":"column")+(e.contentClass!==void 0?` ${e.contentClass}`:"")),h=b(()=>e.disable===!0||r.hasFocus.value===!0||c.value===!1&&r.hasActiveTab.value===!0?-1:e.tabindex||0);function y(m,A){if(A!==!0&&m?.qAvoidFocus!==!0&&s.value?.focus(),e.disable!==!0){r.updateModel({name:e.name}),o("click",m);return}}function P(m){lt(m,[13,32])?y(m,!0):qe(m)!==!0&&m.keyCode>=35&&m.keyCode<=40&&m.altKey!==!0&&m.metaKey!==!0&&r.onKbdNavigate(m.keyCode,n.$el)===!0&&ce(m),o("keydown",m)}function z(){const m=r.tabProps.value.narrowIndicator,A=[],T=p("div",{ref:w,class:["q-tab__indicator",r.tabProps.value.indicatorClass]});e.icon!==void 0&&A.push(p(de,{class:"q-tab__icon",name:e.icon})),e.label!==void 0&&A.push(p("div",{class:"q-tab__label"},e.label)),e.alert!==!1&&A.push(e.alertIcon!==void 0?p(de,{class:"q-tab__alert-icon",color:e.alert!==!0?e.alert:void 0,name:e.alertIcon}):p("div",{class:"q-tab__alert"+(e.alert!==!0?` text-${e.alert}`:"")})),m===!0&&A.push(T);const M=[p("div",{class:"q-focus-helper",tabindex:-1,ref:s}),p("div",{class:d.value},rt(t.default,A))];return m===!1&&M.push(T),M}const B={name:b(()=>e.name),rootRef:_,tabIndicatorRef:w,routeData:a};Pe(()=>{r.unregisterTab(B)}),Ue(()=>{r.registerTab(B)});function W(m,A){const T={ref:_,class:q.value,tabindex:h.value,role:"tab","aria-selected":c.value===!0?"true":"false","aria-disabled":e.disable===!0?"true":void 0,onClick:y,onKeydown:P,...A};return it(p(m,T,z()),[[at,$.value]])}return{renderTab:W,$tabs:r}}const ro=oe({name:"QTab",props:Et,emits:Lt,setup(e,{slots:t,emit:o}){const{renderTab:a}=It(e,t,o);return()=>a("div")}});function zt(e,t,o){const a=o===!0?["left","right"]:["top","bottom"];return`absolute-${t===!0?a[0]:a[1]}${e?` text-${e}`:""}`}const Ot=["left","center","right","justify"],lo=oe({name:"QTabs",props:{modelValue:[Number,String],align:{type:String,default:"center",validator:e=>Ot.includes(e)},breakpoint:{type:[String,Number],default:600},vertical:Boolean,shrink:Boolean,stretch:Boolean,activeClass:String,activeColor:String,activeBgColor:String,indicatorColor:String,leftIcon:String,rightIcon:String,outsideArrows:Boolean,mobileArrows:Boolean,switchIndicator:Boolean,narrowIndicator:Boolean,inlineLabel:Boolean,noCaps:Boolean,dense:Boolean,contentClass:String,"onUpdate:modelValue":[Function,Array]},setup(e,{slots:t,emit:o}){const{proxy:a}=te(),{$q:r}=a,{registerTick:n}=ye(),{registerTick:s}=ye(),{registerTick:_}=ye(),{registerTimeout:w,removeTimeout:$}=Se(),{registerTimeout:c,removeTimeout:q}=Se(),d=O(null),h=O(null),y=O(e.modelValue),P=O(!1),z=O(!0),B=O(!1),W=O(!1),m=[],A=O(0),T=O(!1);let M=null,H=null,D;const J=b(()=>({activeClass:e.activeClass,activeColor:e.activeColor,activeBgColor:e.activeBgColor,indicatorClass:zt(e.indicatorColor,e.switchIndicator,e.vertical),narrowIndicator:e.narrowIndicator,inlineLabel:e.inlineLabel,noCaps:e.noCaps})),Z=b(()=>{const u=A.value,f=y.value;for(let g=0;g<u;g++)if(m[g].name.value===f)return!0;return!1}),ie=b(()=>`q-tabs__content--align-${P.value===!0?"left":W.value===!0?"justify":e.align}`),ae=b(()=>`q-tabs row no-wrap items-center q-tabs--${P.value===!0?"":"not-"}scrollable q-tabs--${e.vertical===!0?"vertical":"horizontal"} q-tabs__arrows--${e.outsideArrows===!0?"outside":"inside"} q-tabs--mobile-with${e.mobileArrows===!0?"":"out"}-arrows`+(e.dense===!0?" q-tabs--dense":"")+(e.shrink===!0?" col-shrink":"")+(e.stretch===!0?" self-stretch":"")),re=b(()=>"q-tabs__content scroll--mobile row no-wrap items-center self-stretch hide-scrollbar relative-position "+ie.value+(e.contentClass!==void 0?` ${e.contentClass}`:"")),v=b(()=>e.vertical===!0?{container:"height",content:"offsetHeight",scroll:"scrollHeight"}:{container:"width",content:"offsetWidth",scroll:"scrollWidth"}),S=b(()=>e.vertical!==!0&&r.lang.rtl===!0),N=b(()=>Ct===!1&&S.value===!0);Y(S,K),Y(()=>e.modelValue,u=>{U({name:u,setCurrent:!0,skipEmit:!0})}),Y(()=>e.outsideArrows,V);function U({name:u,setCurrent:f,skipEmit:g}){y.value!==u&&(g!==!0&&e["onUpdate:modelValue"]!==void 0&&o("update:modelValue",u),(f===!0||e["onUpdate:modelValue"]===void 0)&&(fe(y.value,u),y.value=u))}function V(){n(()=>{d.value&&le({width:d.value.offsetWidth,height:d.value.offsetHeight})})}function le(u){if(v.value===void 0||h.value===null)return;const f=u[v.value.container],g=Math.min(h.value[v.value.scroll],Array.prototype.reduce.call(h.value.children,(E,k)=>E+(k[v.value.content]||0),0)),L=f>0&&g>f;P.value=L,L===!0&&s(K),W.value=f<parseInt(e.breakpoint,10)}function fe(u,f){const g=u!=null&&u!==""?m.find(E=>E.name.value===u):null,L=f!=null&&f!==""?m.find(E=>E.name.value===f):null;if(ve===!0)ve=!1;else if(g&&L){const E=g.tabIndicatorRef.value,k=L.tabIndicatorRef.value;M!==null&&(clearTimeout(M),M=null),E.style.transition="none",E.style.transform="none",k.style.transition="none",k.style.transform="none";const C=E.getBoundingClientRect(),R=k.getBoundingClientRect();k.style.transform=e.vertical===!0?`translate3d(0,${C.top-R.top}px,0) scale3d(1,${R.height?C.height/R.height:1},1)`:`translate3d(${C.left-R.left}px,0,0) scale3d(${R.width?C.width/R.width:1},1,1)`,_(()=>{M=setTimeout(()=>{M=null,k.style.transition="transform .25s cubic-bezier(.4, 0, .2, 1)",k.style.transform="none"},70)})}L&&P.value===!0&&Q(L.rootRef.value)}function Q(u){const{left:f,width:g,top:L,height:E}=h.value.getBoundingClientRect(),k=u.getBoundingClientRect();let C=e.vertical===!0?k.top-L:k.left-f;if(C<0){h.value[e.vertical===!0?"scrollTop":"scrollLeft"]+=Math.floor(C),K();return}C+=e.vertical===!0?k.height-E:k.width-g,C>0&&(h.value[e.vertical===!0?"scrollTop":"scrollLeft"]+=Math.ceil(C),K())}function K(){const u=h.value;if(u===null)return;const f=u.getBoundingClientRect(),g=e.vertical===!0?u.scrollTop:Math.abs(u.scrollLeft);S.value===!0?(z.value=Math.ceil(g+f.width)<u.scrollWidth-1,B.value=g>0):(z.value=g>0,B.value=e.vertical===!0?Math.ceil(g+f.height)<u.scrollHeight:Math.ceil(g+f.width)<u.scrollWidth)}function j(u){H!==null&&clearInterval(H),H=setInterval(()=>{I(u)===!0&&i()},5)}function ee(){j(N.value===!0?Number.MAX_SAFE_INTEGER:0)}function pe(){j(N.value===!0?0:Number.MAX_SAFE_INTEGER)}function i(){H!==null&&(clearInterval(H),H=null)}function l(u,f){const g=Array.prototype.filter.call(h.value.children,R=>R===f||R.matches&&R.matches(".q-tab.q-focusable")===!0),L=g.length;if(L===0)return;if(u===36)return Q(g[0]),g[0].focus(),!0;if(u===35)return Q(g[L-1]),g[L-1].focus(),!0;const E=u===(e.vertical===!0?38:37),k=u===(e.vertical===!0?40:39),C=E===!0?-1:k===!0?1:void 0;if(C!==void 0){const R=S.value===!0?-1:1,G=g.indexOf(f)+C*R;return G>=0&&G<L&&(Q(g[G]),g[G].focus({preventScroll:!0})),!0}}const x=b(()=>N.value===!0?{get:u=>Math.abs(u.scrollLeft),set:(u,f)=>{u.scrollLeft=-f}}:e.vertical===!0?{get:u=>u.scrollTop,set:(u,f)=>{u.scrollTop=f}}:{get:u=>u.scrollLeft,set:(u,f)=>{u.scrollLeft=f}});function I(u){const f=h.value,{get:g,set:L}=x.value;let E=!1,k=g(f);const C=u<k?-1:1;return k+=C*5,k<0?(E=!0,k=0):(C===-1&&k<=u||C===1&&k>=u)&&(E=!0,k=u),L(f,k),K(),E}function F(u,f){for(const g in u)if(u[g]!==f[g])return!1;return!0}function _e(){let u=null,f={matchedLen:0,queryDiff:9999,hrefLen:0};const g=m.filter(C=>C.routeData?.hasRouterLink.value===!0),{hash:L,query:E}=a.$route,k=Object.keys(E).length;for(const C of g){const R=C.routeData.exact.value===!0;if(C.routeData[R===!0?"linkIsExactActive":"linkIsActive"].value!==!0)continue;const{hash:G,query:me,matched:tt,href:ot}=C.routeData.resolvedLink.value,ge=Object.keys(me).length;if(R===!0){if(G!==L||ge!==k||F(E,me)===!1)continue;u=C.name.value;break}if(G!==""&&G!==L||ge!==0&&F(me,E)===!1)continue;const X={matchedLen:tt.length,queryDiff:k-ge,hrefLen:ot.length-G.length};if(X.matchedLen>f.matchedLen){u=C.name.value,f=X;continue}else if(X.matchedLen!==f.matchedLen)continue;if(X.queryDiff<f.queryDiff)u=C.name.value,f=X;else if(X.queryDiff!==f.queryDiff)continue;X.hrefLen>f.hrefLen&&(u=C.name.value,f=X)}if(u===null&&m.some(C=>C.routeData===void 0&&C.name.value===y.value)===!0){ve=!1;return}U({name:u,setCurrent:!0})}function Je(u){if($(),T.value!==!0&&d.value!==null&&u.target&&typeof u.target.closest=="function"){const f=u.target.closest(".q-tab");f&&d.value.contains(f)===!0&&(T.value=!0,P.value===!0&&Q(f))}}function Ze(){w(()=>{T.value=!1},30)}function se(){Ie.avoidRouteWatcher===!1?c(_e):q()}function Ee(){if(D===void 0){const u=Y(()=>a.$route.fullPath,se);D=()=>{u(),D=void 0}}}function Ve(u){m.push(u),A.value++,V(),u.routeData===void 0||a.$route===void 0?c(()=>{if(P.value===!0){const f=y.value,g=f!=null&&f!==""?m.find(L=>L.name.value===f):null;g&&Q(g.rootRef.value)}}):(Ee(),u.routeData.hasRouterLink.value===!0&&se())}function et(u){m.splice(m.indexOf(u),1),A.value--,V(),D!==void 0&&u.routeData!==void 0&&(m.every(f=>f.routeData===void 0)===!0&&D(),se())}const Ie={currentModel:y,tabProps:J,hasFocus:T,hasActiveTab:Z,registerTab:Ve,unregisterTab:et,verifyRouteModel:se,updateModel:U,onKbdNavigate:l,avoidRouteWatcher:!1};ct(je,Ie);function ze(){M!==null&&clearTimeout(M),i(),D?.()}let Oe,ve;return Pe(ze),st(()=>{Oe=D!==void 0,ze()}),ut(()=>{Oe===!0&&(Ee(),ve=!0,se()),V()}),()=>p("div",{ref:d,class:ae.value,role:"tablist",onFocusin:Je,onFocusout:Ze},[p(xt,{onResize:le}),p("div",{ref:h,class:re.value,onScroll:K},ne(t.default)),p(de,{class:"q-tabs__arrow q-tabs__arrow--left absolute q-tab__icon"+(z.value===!0?"":" q-tabs__arrow--faded"),name:e.leftIcon||r.iconSet.tabs[e.vertical===!0?"up":"left"],onMousedownPassive:ee,onTouchstartPassive:ee,onMouseupPassive:i,onMouseleavePassive:i,onTouchendPassive:i}),p(de,{class:"q-tabs__arrow q-tabs__arrow--right absolute q-tab__icon"+(B.value===!0?"":" q-tabs__arrow--faded"),name:e.rightIcon||r.iconSet.tabs[e.vertical===!0?"down":"right"],onMousedownPassive:pe,onTouchstartPassive:pe,onMouseupPassive:i,onMouseleavePassive:i,onTouchendPassive:i})])}});function Bt(e){const t=[.06,6,50];return typeof e=="string"&&e.length&&e.split(":").forEach((o,a)=>{const r=parseFloat(o);r&&(t[a]=r)}),t}const Dt=dt({name:"touch-swipe",beforeMount(e,{value:t,arg:o,modifiers:a}){if(a.mouse!==!0&&ue.has.touch!==!0)return;const r=a.mouseCapture===!0?"Capture":"",n={handler:t,sensitivity:Bt(o),direction:Ne(a),noop:Qe,mouseStart(s){Re(s,n)&&ft(s)&&(he(n,"temp",[[document,"mousemove","move",`notPassive${r}`],[document,"mouseup","end","notPassiveCapture"]]),n.start(s,!0))},touchStart(s){if(Re(s,n)){const _=s.target;he(n,"temp",[[_,"touchmove","move","notPassiveCapture"],[_,"touchcancel","end","notPassiveCapture"],[_,"touchend","end","notPassiveCapture"]]),n.start(s)}},start(s,_){ue.is.firefox===!0&&Ce(e,!0);const w=Be(s);n.event={x:w.left,y:w.top,time:Date.now(),mouse:_===!0,dir:!1}},move(s){if(n.event===void 0)return;if(n.event.dir!==!1){ce(s);return}const _=Date.now()-n.event.time;if(_===0)return;const w=Be(s),$=w.left-n.event.x,c=Math.abs($),q=w.top-n.event.y,d=Math.abs(q);if(n.event.mouse!==!0){if(c<n.sensitivity[1]&&d<n.sensitivity[1]){n.end(s);return}}else if(window.getSelection().toString()!==""){n.end(s);return}else if(c<n.sensitivity[2]&&d<n.sensitivity[2])return;const h=c/_,y=d/_;n.direction.vertical===!0&&c<d&&c<100&&y>n.sensitivity[0]&&(n.event.dir=q<0?"up":"down"),n.direction.horizontal===!0&&c>d&&d<100&&h>n.sensitivity[0]&&(n.event.dir=$<0?"left":"right"),n.direction.up===!0&&c<d&&q<0&&c<100&&y>n.sensitivity[0]&&(n.event.dir="up"),n.direction.down===!0&&c<d&&q>0&&c<100&&y>n.sensitivity[0]&&(n.event.dir="down"),n.direction.left===!0&&c>d&&$<0&&d<100&&h>n.sensitivity[0]&&(n.event.dir="left"),n.direction.right===!0&&c>d&&$>0&&d<100&&h>n.sensitivity[0]&&(n.event.dir="right"),n.event.dir!==!1?(ce(s),n.event.mouse===!0&&(document.body.classList.add("no-pointer-events--children"),document.body.classList.add("non-selectable"),wt(),n.styleCleanup=P=>{n.styleCleanup=void 0,document.body.classList.remove("non-selectable");const z=()=>{document.body.classList.remove("no-pointer-events--children")};P===!0?setTimeout(z,50):z()}),n.handler({evt:s,touch:n.event.mouse!==!0,mouse:n.event.mouse,direction:n.event.dir,duration:_,distance:{x:c,y:d}})):n.end(s)},end(s){n.event!==void 0&&(xe(n,"temp"),ue.is.firefox===!0&&Ce(e,!1),n.styleCleanup?.(!0),s!==void 0&&n.event.dir!==!1&&ce(s),n.event=void 0)}};if(e.__qtouchswipe=n,a.mouse===!0){const s=a.mouseCapture===!0||a.mousecapture===!0?"Capture":"";he(n,"main",[[e,"mousedown","mouseStart",`passive${s}`]])}ue.has.touch===!0&&he(n,"main",[[e,"touchstart","touchStart",`passive${a.capture===!0?"Capture":""}`],[e,"touchmove","noop","notPassiveCapture"]])},updated(e,t){const o=e.__qtouchswipe;o!==void 0&&(t.oldValue!==t.value&&(typeof t.value!="function"&&o.end(),o.handler=t.value),o.direction=Ne(t.modifiers))},beforeUnmount(e){const t=e.__qtouchswipe;t!==void 0&&(xe(t,"main"),xe(t,"temp"),ue.is.firefox===!0&&Ce(e,!1),t.styleCleanup?.(),delete e.__qtouchswipe)}});function Nt(){let e=Object.create(null);return{getCache:(t,o)=>e[t]===void 0?e[t]=typeof o=="function"?o():o:e[t],setCache(t,o){e[t]=o},hasCache(t){return Object.hasOwnProperty.call(e,t)},clearCache(t){t!==void 0?delete e[t]:e=Object.create(null)}}}const Rt={name:{required:!0},disable:Boolean},Fe={setup(e,{slots:t}){return()=>p("div",{class:"q-panel scroll",role:"tabpanel"},ne(t.default))}},Mt={modelValue:{required:!0},animated:Boolean,infinite:Boolean,swipeable:Boolean,vertical:Boolean,transitionPrev:String,transitionNext:String,transitionDuration:{type:[String,Number],default:300},keepAlive:Boolean,keepAliveInclude:[String,Array,RegExp],keepAliveExclude:[String,Array,RegExp],keepAliveMax:Number},Ft=["update:modelValue","beforeTransition","transition"];function Ht(){const{props:e,emit:t,proxy:o}=te(),{getCache:a}=Nt(),{registerTimeout:r}=Se();let n,s;const _=O(null),w={value:null};function $(v){const S=e.vertical===!0?"up":"left";D((o.$q.lang.rtl===!0?-1:1)*(v.direction===S?1:-1))}const c=b(()=>[[Dt,$,void 0,{horizontal:e.vertical!==!0,vertical:e.vertical,mouse:!0}]]),q=b(()=>e.transitionPrev||`slide-${e.vertical===!0?"down":"right"}`),d=b(()=>e.transitionNext||`slide-${e.vertical===!0?"up":"left"}`),h=b(()=>`--q-transition-duration: ${e.transitionDuration}ms`),y=b(()=>typeof e.modelValue=="string"||typeof e.modelValue=="number"?e.modelValue:String(e.modelValue)),P=b(()=>({include:e.keepAliveInclude,exclude:e.keepAliveExclude,max:e.keepAliveMax})),z=b(()=>e.keepAliveInclude!==void 0||e.keepAliveExclude!==void 0);Y(()=>e.modelValue,(v,S)=>{const N=A(v)===!0?T(v):-1;s!==!0&&H(N===-1?0:N<T(S)?-1:1),w.value!==N&&(w.value=N,t("beforeTransition",v,S),r(()=>{t("transition",v,S)},e.transitionDuration))});function B(){D(1)}function W(){D(-1)}function m(v){t("update:modelValue",v)}function A(v){return v!=null&&v!==""}function T(v){return n.findIndex(S=>S.props.name===v&&S.props.disable!==""&&S.props.disable!==!0)}function M(){return n.filter(v=>v.props.disable!==""&&v.props.disable!==!0)}function H(v){const S=v!==0&&e.animated===!0&&w.value!==-1?"q-transition--"+(v===-1?q.value:d.value):null;_.value!==S&&(_.value=S)}function D(v,S=w.value){let N=S+v;for(;N!==-1&&N<n.length;){const U=n[N];if(U!==void 0&&U.props.disable!==""&&U.props.disable!==!0){H(v),s=!0,t("update:modelValue",U.props.name),setTimeout(()=>{s=!1});return}N+=v}e.infinite===!0&&n.length!==0&&S!==-1&&S!==n.length&&D(v,v===-1?n.length:-1)}function J(){const v=T(e.modelValue);return w.value!==v&&(w.value=v),!0}function Z(){const v=A(e.modelValue)===!0&&J()&&n[w.value];return e.keepAlive===!0?[p(ht,P.value,[p(z.value===!0?a(y.value,()=>({...Fe,name:y.value})):Fe,{key:y.value,style:h.value},()=>v)])]:[p("div",{class:"q-panel scroll",style:h.value,key:y.value,role:"tabpanel"},[v])]}function ie(){if(n.length!==0)return e.animated===!0?[p(pt,{name:_.value},Z)]:Z()}function ae(v){return n=vt(ne(v.default,[])).filter(S=>S.props!==null&&S.props.slot===void 0&&A(S.props.name)===!0),n.length}function re(){return n}return Object.assign(o,{next:B,previous:W,goTo:m}),{panelIndex:w,panelDirectives:c,updatePanelsList:ae,updatePanelIndex:J,getPanelContent:ie,getEnabledPanels:M,getPanels:re,isValidPanelName:A,keepAliveProps:P,needsUniqueKeepAliveWrapper:z,goToPanelByOffset:D,goToPanel:m,nextPanel:B,previousPanel:W}}const so=oe({name:"QTabPanel",props:Rt,setup(e,{slots:t}){return()=>p("div",{class:"q-tab-panel",role:"tabpanel"},ne(t.default))}}),uo=oe({name:"QTabPanels",props:{...Mt,...$e},emits:Ft,setup(e,{slots:t}){const o=te(),a=Ae(e,o.proxy.$q),{updatePanelsList:r,getPanelContent:n,panelDirectives:s}=Ht(),_=b(()=>"q-tab-panels q-panel-parent"+(a.value===!0?" q-tab-panels--dark q-dark":""));return()=>(r(t),_t("div",{class:_.value},n(),"pan",e.swipeable,()=>s.value))}});function Ke(e,t){if(t&&e===t)return null;const o=e.nodeName.toLowerCase();if(["div","li","ul","ol","blockquote"].includes(o)===!0)return e;const a=window.getComputedStyle?window.getComputedStyle(e):e.currentStyle,r=a.display;return r==="block"||r==="table"?e:Ke(e.parentNode)}function we(e,t,o){return!e||e===document.body?!1:o===!0&&e===t||(t===document?document.body:t).contains(e.parentNode)}function We(e,t,o){if(o||(o=document.createRange(),o.selectNode(e),o.setStart(e,0)),t.count===0)o.setEnd(e,t.count);else if(t.count>0)if(e.nodeType===Node.TEXT_NODE)e.textContent.length<t.count?t.count-=e.textContent.length:(o.setEnd(e,t.count),t.count=0);else for(let a=0;t.count!==0&&a<e.childNodes.length;a++)o=We(e.childNodes[a],t,o);return o}const jt=/^https?:\/\//;class Ut{constructor(t,o){this.el=t,this.eVm=o,this._range=null}get selection(){if(this.el){const t=document.getSelection();if(we(t.anchorNode,this.el,!0)&&we(t.focusNode,this.el,!0))return t}return null}get hasSelection(){return this.selection!==null?this.selection.toString().length!==0:!1}get range(){const t=this.selection;return t?.rangeCount?t.getRangeAt(0):this._range}get parent(){const t=this.range;if(t!==null){const o=t.startContainer;return o.nodeType===document.ELEMENT_NODE?o:o.parentNode}return null}get blockParent(){const t=this.parent;return t!==null?Ke(t,this.el):null}save(t=this.range){t!==null&&(this._range=t)}restore(t=this._range){const o=document.createRange(),a=document.getSelection();t!==null?(o.setStart(t.startContainer,t.startOffset),o.setEnd(t.endContainer,t.endOffset),a.removeAllRanges(),a.addRange(o)):(a.selectAllChildren(this.el),a.collapseToEnd())}savePosition(){let t=-1,o;const a=document.getSelection(),r=this.el.parentNode;if(a.focusNode&&we(a.focusNode,r))for(o=a.focusNode,t=a.focusOffset;o&&o!==r;)o!==this.el&&o.previousSibling?(o=o.previousSibling,t+=o.textContent.length):o=o.parentNode;this.savedPos=t}restorePosition(t=0){if(this.savedPos>0&&this.savedPos<t){const o=window.getSelection(),a=We(this.el,{count:this.savedPos});a&&(a.collapse(!1),o.removeAllRanges(),o.addRange(a))}}hasParent(t,o){const a=o?this.parent:this.blockParent;return a!==null?a.nodeName.toLowerCase()===t.toLowerCase():!1}hasParents(t,o,a=this.parent){return a===null?!1:t.includes(a.nodeName.toLowerCase())===!0?!0:o===!0?this.hasParents(t,o,a.parentNode):!1}is(t,o){if(this.selection===null)return!1;switch(t){case"formatBlock":return o==="DIV"&&this.parent===this.el||this.hasParent(o,o==="PRE");case"link":return this.hasParent("A",!0);case"fontSize":return document.queryCommandValue(t)===o;case"fontName":const a=document.queryCommandValue(t);return a===`"${o}"`||a===o;case"fullscreen":return this.eVm.inFullscreen.value;case"viewsource":return this.eVm.isViewingSource.value;case void 0:return!1;default:const r=document.queryCommandState(t);return o!==void 0?r===o:r}}getParentAttribute(t){return this.parent!==null?this.parent.getAttribute(t):null}can(t){if(t==="outdent")return this.hasParents(["blockquote","li"],!0);if(t==="indent")return this.hasParents(["li"],!0);if(t==="link")return this.selection!==null||this.is("link")}apply(t,o,a=Qe){if(t==="formatBlock")["BLOCKQUOTE","H1","H2","H3","H4","H5","H6"].includes(o)&&this.is(t,o)&&(t="outdent",o=null),o==="PRE"&&this.is(t,"PRE")&&(o="P");else if(t==="print"){a();const r=window.open();r.document.write(`
        <!doctype html>
        <html>
          <head>
            <title>Print - ${document.title}</title>
          </head>
          <body>
            <div>${this.el.innerHTML}</div>
          </body>
        </html>
      `),r.print(),r.close();return}else if(t==="link"){const r=this.getParentAttribute("href");if(r===null){const n=this.selectWord(this.selection),s=n?n.toString():"";if(!s.length&&(!this.range||!this.range.cloneContents().querySelector("img")))return;this.eVm.editLinkUrl.value=jt.test(s)?s:"https://",document.execCommand("createLink",!1,this.eVm.editLinkUrl.value),this.save(n.getRangeAt(0))}else this.eVm.editLinkUrl.value=r,this.range.selectNodeContents(this.parent),this.save();return}else if(t==="fullscreen"){this.eVm.toggleFullscreen(),a();return}else if(t==="viewsource"){this.eVm.isViewingSource.value=this.eVm.isViewingSource.value===!1,this.eVm.setContent(this.eVm.props.modelValue),a();return}document.execCommand(t,!1,o),a()}selectWord(t){if(t===null||t.isCollapsed!==!0||t.modify===void 0)return t;const o=document.createRange();o.setStart(t.anchorNode,t.anchorOffset),o.setEnd(t.focusNode,t.focusOffset);const a=o.collapsed?["backward","forward"]:["forward","backward"];o.detach();const r=t.focusNode,n=t.focusOffset;return t.collapse(t.anchorNode,t.anchorOffset),t.modify("move",a[0],"character"),t.modify("move",a[1],"word"),t.extend(r,n),t.modify("extend",a[1],"character"),t.modify("extend",a[0],"word"),t}}function Ge(e,t,o){t.handler?t.handler(e,o,o.caret):o.runCmd(t.cmd,t.param)}function Le(e){return p("div",{class:"q-editor__toolbar-group"},e)}function Xe(e,t,o,a=!1){const r=a||(t.type==="toggle"?t.toggled?t.toggled(e):t.cmd&&e.caret.is(t.cmd,t.param):!1),n=[];if(t.tip&&e.$q.platform.is.desktop){const s=t.key?p("div",[p("small",`(CTRL + ${String.fromCharCode(t.key)})`)]):null;n.push(p(St,{delay:1e3},()=>[p("div",{innerHTML:t.tip}),s]))}return p(ke,{...e.buttonProps.value,icon:t.icon!==null?t.icon:void 0,color:r?t.toggleColor||e.props.toolbarToggleColor:t.color||e.props.toolbarColor,textColor:r&&!e.props.toolbarPush?null:t.textColor||e.props.toolbarTextColor,label:t.label,disable:t.disable?typeof t.disable=="function"?t.disable(e):!0:!1,size:"sm",onClick(s){o?.(),Ge(s,t,e)}},()=>n)}function Qt(e,t){const o=t.list==="only-icons";let a=t.label,r=t.icon!==null?t.icon:void 0,n,s;function _(){$.component.proxy.hide()}if(o)s=t.options.map(c=>{const q=c.type===void 0?e.caret.is(c.cmd,c.param):!1;return q&&(a=c.tip,r=c.icon!==null?c.icon:void 0),Xe(e,c,_,q)}),n=e.toolbarBackgroundClass.value,s=[Le(s)];else{const c=e.props.toolbarToggleColor!==void 0?`text-${e.props.toolbarToggleColor}`:null,q=e.props.toolbarTextColor!==void 0?`text-${e.props.toolbarTextColor}`:null,d=t.list==="no-icons";s=t.options.map(h=>{const y=h.disable?h.disable(e):!1,P=h.type===void 0?e.caret.is(h.cmd,h.param):!1;P&&(a=h.tip,r=h.icon!==null?h.icon:void 0);const z=h.htmlTip;return p(kt,{active:P,activeClass:c,clickable:!0,disable:y,dense:!0,onClick(B){_(),B?.qAvoidFocus!==!0&&e.contentRef.value?.focus(),e.caret.restore(),Ge(B,h,e)}},()=>[d===!0?null:p(Me,{class:P?c:q,side:!0},()=>p(de,{name:h.icon!==null?h.icon:void 0})),p(Me,z?()=>p("div",{class:"text-no-wrap",innerHTML:h.htmlTip}):h.tip?()=>p("div",{class:"text-no-wrap"},h.tip):void 0)])}),n=[e.toolbarBackgroundClass.value,q]}const w=t.highlight&&a!==t.label,$=p(Tt,{...e.buttonProps.value,noCaps:!0,noWrap:!0,color:w?e.props.toolbarToggleColor:e.props.toolbarColor,textColor:w&&!e.props.toolbarPush?null:e.props.toolbarTextColor,label:t.fixedLabel?t.label:a,icon:t.fixedIcon?t.icon!==null?t.icon:void 0:r,contentClass:n,onShow:c=>e.emit("dropdownShow",c),onHide:c=>e.emit("dropdownHide",c),onBeforeShow:c=>e.emit("dropdownBeforeShow",c),onBeforeHide:c=>e.emit("dropdownBeforeHide",c)},()=>s);return $}function Kt(e){if(e.caret)return e.buttons.value.filter(t=>!e.isViewingSource.value||t.find(o=>o.cmd==="viewsource")).map(t=>Le(t.map(o=>e.isViewingSource.value&&o.cmd!=="viewsource"?!1:o.type==="slot"?ne(e.slots[o.slot]):o.type==="dropdown"?Qt(e,o):Xe(e,o))))}function Wt(e,t,o,a={}){const r=Object.keys(a);if(r.length===0)return{};const n={default_font:{cmd:"fontName",param:e,icon:o,tip:t}};return r.forEach(s=>{const _=a[s];n[s]={cmd:"fontName",param:_,icon:o,tip:_,htmlTip:`<font face="${_}">${_}</font>`}}),n}function Gt(e){if(e.caret){const t=e.props.toolbarColor||e.props.toolbarTextColor;let o=e.editLinkUrl.value;const a=()=>{e.caret.restore(),o!==e.editLinkUrl.value&&document.execCommand("createLink",!1,o===""?" ":o),e.editLinkUrl.value=null};return[p("div",{class:`q-mx-xs text-${t}`},`${e.$q.lang.editor.url}: `),p("input",{key:"qedt_btm_input",class:"col q-editor__link-input",value:o,onInput:r=>{mt(r),o=r.target.value},onKeydown:r=>{if(qe(r)!==!0)switch(r.keyCode){case 13:return De(r),a();case 27:De(r),e.caret.restore(),(!e.editLinkUrl.value||e.editLinkUrl.value==="https://")&&document.execCommand("unlink"),e.editLinkUrl.value=null;break}}}),Le([p(ke,{key:"qedt_btm_rem",tabindex:-1,...e.buttonProps.value,label:e.$q.lang.label.remove,noCaps:!0,onClick:()=>{e.caret.restore(),document.execCommand("unlink"),e.editLinkUrl.value=null}}),p(ke,{key:"qedt_btm_upd",...e.buttonProps.value,label:e.$q.lang.label.update,noCaps:!0,onClick:a})])]}}const Xt=Object.prototype.toString,Te=Object.prototype.hasOwnProperty,Yt=new Set(["Boolean","Number","String","Function","Array","Date","RegExp"].map(e=>"[object "+e+"]"));function He(e){if(e!==Object(e)||Yt.has(Xt.call(e))===!0||e.constructor&&Te.call(e,"constructor")===!1&&Te.call(e.constructor.prototype,"isPrototypeOf")===!1)return!1;let t;for(t in e);return t===void 0||Te.call(e,t)}function Ye(){let e,t,o,a,r,n,s=arguments[0]||{},_=1,w=!1;const $=arguments.length;for(typeof s=="boolean"&&(w=s,s=arguments[1]||{},_=2),Object(s)!==s&&typeof s!="function"&&(s={}),$===_&&(s=this,_--);_<$;_++)if((e=arguments[_])!==null)for(t in e)o=s[t],a=e[t],s!==a&&(w===!0&&a&&((r=Array.isArray(a))||He(a)===!0)?(r===!0?n=Array.isArray(o)===!0?o:[]:n=He(o)===!0?o:{},s[t]=Ye(w,n,a)):a!==void 0&&(s[t]=a));return s}const co=oe({name:"QEditor",props:{...$e,...qt,modelValue:{type:String,required:!0},readonly:Boolean,disable:Boolean,minHeight:{type:String,default:"10rem"},maxHeight:String,height:String,definitions:Object,fonts:Object,placeholder:String,toolbar:{type:Array,validator:e=>e.length===0||e.every(t=>t.length),default:()=>[["left","center","right","justify"],["bold","italic","underline","strike"],["undo","redo"]]},toolbarColor:String,toolbarBg:String,toolbarTextColor:String,toolbarToggleColor:{type:String,default:"primary"},toolbarOutline:Boolean,toolbarPush:Boolean,toolbarRounded:Boolean,paragraphTag:{type:String,validator:e=>["div","p"].includes(e),default:"div"},contentStyle:Object,contentClass:[Object,Array,String],square:Boolean,flat:Boolean,dense:Boolean},emits:[...Pt,"update:modelValue","keydown","click","focus","blur","dropdownShow","dropdownHide","dropdownBeforeShow","dropdownBeforeHide","linkShow","linkHide"],setup(e,{slots:t,emit:o}){const{proxy:a}=te(),{$q:r}=a,n=Ae(e,r),{inFullscreen:s,toggleFullscreen:_}=$t(),w=gt(),$=O(null),c=O(null),q=O(null),d=O(!1),h=b(()=>!e.readonly&&!e.disable);let y,P,z=e.modelValue;document.execCommand("defaultParagraphSeparator",!1,e.paragraphTag),y=window.getComputedStyle(document.body).fontFamily;const B=b(()=>e.toolbarBg?` bg-${e.toolbarBg}`:""),W=b(()=>({type:"a",flat:e.toolbarOutline!==!0&&e.toolbarPush!==!0,noWrap:!0,outline:e.toolbarOutline,push:e.toolbarPush,rounded:e.toolbarRounded,dense:!0,color:e.toolbarColor,disable:!h.value,size:"sm"})),m=b(()=>{const i=r.lang.editor,l=r.iconSet.editor;return{bold:{cmd:"bold",icon:l.bold,tip:i.bold,key:66},italic:{cmd:"italic",icon:l.italic,tip:i.italic,key:73},strike:{cmd:"strikeThrough",icon:l.strikethrough,tip:i.strikethrough,key:83},underline:{cmd:"underline",icon:l.underline,tip:i.underline,key:85},unordered:{cmd:"insertUnorderedList",icon:l.unorderedList,tip:i.unorderedList},ordered:{cmd:"insertOrderedList",icon:l.orderedList,tip:i.orderedList},subscript:{cmd:"subscript",icon:l.subscript,tip:i.subscript,htmlTip:"x<subscript>2</subscript>"},superscript:{cmd:"superscript",icon:l.superscript,tip:i.superscript,htmlTip:"x<superscript>2</superscript>"},link:{cmd:"link",disable:x=>x.caret&&!x.caret.can("link"),icon:l.hyperlink,tip:i.hyperlink,key:76},fullscreen:{cmd:"fullscreen",icon:l.toggleFullscreen,tip:i.toggleFullscreen,key:70},viewsource:{cmd:"viewsource",icon:l.viewSource,tip:i.viewSource},quote:{cmd:"formatBlock",param:"BLOCKQUOTE",icon:l.quote,tip:i.quote,key:81},left:{cmd:"justifyLeft",icon:l.left,tip:i.left},center:{cmd:"justifyCenter",icon:l.center,tip:i.center},right:{cmd:"justifyRight",icon:l.right,tip:i.right},justify:{cmd:"justifyFull",icon:l.justify,tip:i.justify},print:{type:"no-state",cmd:"print",icon:l.print,tip:i.print,key:80},outdent:{type:"no-state",disable:x=>x.caret&&!x.caret.can("outdent"),cmd:"outdent",icon:l.outdent,tip:i.outdent},indent:{type:"no-state",disable:x=>x.caret&&!x.caret.can("indent"),cmd:"indent",icon:l.indent,tip:i.indent},removeFormat:{type:"no-state",cmd:"removeFormat",icon:l.removeFormat,tip:i.removeFormat},hr:{type:"no-state",cmd:"insertHorizontalRule",icon:l.hr,tip:i.hr},undo:{type:"no-state",cmd:"undo",icon:l.undo,tip:i.undo,key:90},redo:{type:"no-state",cmd:"redo",icon:l.redo,tip:i.redo,key:89},h1:{cmd:"formatBlock",param:"H1",icon:l.heading1||l.heading,tip:i.heading1,htmlTip:`<h1 class="q-ma-none">${i.heading1}</h1>`},h2:{cmd:"formatBlock",param:"H2",icon:l.heading2||l.heading,tip:i.heading2,htmlTip:`<h2 class="q-ma-none">${i.heading2}</h2>`},h3:{cmd:"formatBlock",param:"H3",icon:l.heading3||l.heading,tip:i.heading3,htmlTip:`<h3 class="q-ma-none">${i.heading3}</h3>`},h4:{cmd:"formatBlock",param:"H4",icon:l.heading4||l.heading,tip:i.heading4,htmlTip:`<h4 class="q-ma-none">${i.heading4}</h4>`},h5:{cmd:"formatBlock",param:"H5",icon:l.heading5||l.heading,tip:i.heading5,htmlTip:`<h5 class="q-ma-none">${i.heading5}</h5>`},h6:{cmd:"formatBlock",param:"H6",icon:l.heading6||l.heading,tip:i.heading6,htmlTip:`<h6 class="q-ma-none">${i.heading6}</h6>`},p:{cmd:"formatBlock",param:e.paragraphTag,icon:l.heading,tip:i.paragraph},code:{cmd:"formatBlock",param:"PRE",icon:l.code,htmlTip:`<code>${i.code}</code>`},"size-1":{cmd:"fontSize",param:"1",icon:l.size1||l.size,tip:i.size1,htmlTip:`<font size="1">${i.size1}</font>`},"size-2":{cmd:"fontSize",param:"2",icon:l.size2||l.size,tip:i.size2,htmlTip:`<font size="2">${i.size2}</font>`},"size-3":{cmd:"fontSize",param:"3",icon:l.size3||l.size,tip:i.size3,htmlTip:`<font size="3">${i.size3}</font>`},"size-4":{cmd:"fontSize",param:"4",icon:l.size4||l.size,tip:i.size4,htmlTip:`<font size="4">${i.size4}</font>`},"size-5":{cmd:"fontSize",param:"5",icon:l.size5||l.size,tip:i.size5,htmlTip:`<font size="5">${i.size5}</font>`},"size-6":{cmd:"fontSize",param:"6",icon:l.size6||l.size,tip:i.size6,htmlTip:`<font size="6">${i.size6}</font>`},"size-7":{cmd:"fontSize",param:"7",icon:l.size7||l.size,tip:i.size7,htmlTip:`<font size="7">${i.size7}</font>`}}}),A=b(()=>{const i=e.definitions||{},l=e.definitions||e.fonts?Ye(!0,{},m.value,i,Wt(y,r.lang.editor.defaultFont,r.iconSet.editor.font,e.fonts)):m.value;return e.toolbar.map(x=>x.map(I=>{if(I.options)return{type:"dropdown",icon:I.icon,label:I.label,size:"sm",dense:!0,fixedLabel:I.fixedLabel,fixedIcon:I.fixedIcon,highlight:I.highlight,list:I.list,options:I.options.map(_e=>l[_e])};const F=l[I];return F?F.type==="no-state"||i[I]&&(F.cmd===void 0||m.value[F.cmd]&&m.value[F.cmd].type==="no-state")?F:Object.assign({type:"toggle"},F):{type:"slot",slot:I}}))}),T={$q:r,props:e,slots:t,emit:o,inFullscreen:s,toggleFullscreen:_,runCmd:K,isViewingSource:d,editLinkUrl:q,toolbarBackgroundClass:B,buttonProps:W,contentRef:c,buttons:A,setContent:Q};Y(()=>e.modelValue,i=>{z!==i&&(z=i,Q(i,!0))}),Y(q,i=>{o(`link${i?"Show":"Hide"}`)});const M=b(()=>e.toolbar&&e.toolbar.length!==0),H=b(()=>{const i={},l=x=>{x.key&&(i[x.key]={cmd:x.cmd,param:x.param})};return A.value.forEach(x=>{x.forEach(I=>{I.options?I.options.forEach(l):l(I)})}),i}),D=b(()=>s.value?e.contentStyle:[{minHeight:e.minHeight,height:e.height,maxHeight:e.maxHeight},e.contentStyle]),J=b(()=>`q-editor q-editor--${d.value===!0?"source":"default"}`+(e.disable===!0?" disabled":"")+(s.value===!0?" fullscreen column":"")+(e.square===!0?" q-editor--square no-border-radius":"")+(e.flat===!0?" q-editor--flat":"")+(e.dense===!0?" q-editor--dense":"")+(n.value===!0?" q-editor--dark q-dark":"")),Z=b(()=>[e.contentClass,"q-editor__content",{col:s.value,"overflow-auto":s.value||e.maxHeight}]),ie=b(()=>e.disable===!0?{"aria-disabled":"true"}:{});function ae(){if(c.value!==null){const i=`inner${d.value===!0?"Text":"HTML"}`,l=c.value[i];l!==e.modelValue&&(z=l,o("update:modelValue",l))}}function re(i){if(o("keydown",i),i.ctrlKey!==!0||qe(i)===!0){j();return}const l=i.keyCode,x=H.value[l];if(x!==void 0){const{cmd:I,param:F}=x;ce(i),K(I,F,!1)}}function v(i){j(),o("click",i)}function S(i){if(c.value!==null){const{scrollTop:l,scrollHeight:x}=c.value;P=x-l}T.caret.save(),o("blur",i)}function N(i){bt(()=>{c.value!==null&&P!==void 0&&(c.value.scrollTop=c.value.scrollHeight-P)}),o("focus",i)}function U(i){const l=$.value;if(l!==null&&l.contains(i.target)===!0&&(i.relatedTarget===null||l.contains(i.relatedTarget)!==!0)){const x=`inner${d.value===!0?"Text":"HTML"}`;T.caret.restorePosition(c.value[x].length),j()}}function V(i){const l=$.value;l!==null&&l.contains(i.target)===!0&&(i.relatedTarget===null||l.contains(i.relatedTarget)!==!0)&&(T.caret.savePosition(),j())}function le(){P=void 0}function fe(i){T.caret.save()}function Q(i,l){if(c.value!==null){l===!0&&T.caret.savePosition();const x=`inner${d.value===!0?"Text":"HTML"}`;c.value[x]=i,l===!0&&(T.caret.restorePosition(c.value[x].length),j())}}function K(i,l,x=!0){ee(),T.caret.restore(),T.caret.apply(i,l,()=>{ee(),T.caret.save(),x&&j()})}function j(){setTimeout(()=>{q.value=null,a.$forceUpdate()},1)}function ee(){yt(()=>{c.value?.focus({preventScroll:!0})})}function pe(){return c.value}return Ue(()=>{T.caret=a.caret=new Ut(c.value,T),Q(e.modelValue),j(),document.addEventListener("selectionchange",fe)}),Pe(()=>{document.removeEventListener("selectionchange",fe)}),Object.assign(a,{runCmd:K,refreshToolbar:j,focus:ee,getContentEl:pe}),()=>{let i;if(M.value){const l=[p("div",{key:"qedt_top",class:"q-editor__toolbar row no-wrap scroll-x"+B.value},Kt(T))];q.value!==null&&l.push(p("div",{key:"qedt_btm",class:"q-editor__toolbar row no-wrap items-center scroll-x"+B.value},Gt(T))),i=p("div",{key:"toolbar_ctainer",class:"q-editor__toolbars-container"},l)}return p("div",{ref:$,class:J.value,style:{height:s.value===!0?"100%":null},...ie.value,onFocusin:U,onFocusout:V},[i,p("div",{ref:c,style:D.value,class:Z.value,contenteditable:h.value,placeholder:e.placeholder,...w.listeners.value,onInput:ae,onKeydown:re,onClick:v,onBlur:S,onFocus:N,onMousedown:le,onTouchstartPassive:le})])}}}),fo={acta({casoId:e,fecha:t,titulo:o,lugar:a,tipo:r}){return`
<div style="border:1px solid #333;border-radius:6px;padding:12px;margin-bottom:12px;font-size:12px">
  <div style="display:flex;justify-content:space-between">
    <div>
      <div style="font-weight:700">DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES (D.I.O.)</div>
      <div style="font-size:11px;color:#555">Servicio Legal Integral Municipal</div>
    </div>
    <div style="text-align:right;font-size:11px;color:#555">
      <div><b>Caso:</b> ${e}</div>
      <div><b>Fecha sesión:</b> ${t}</div>
    </div>
  </div>
</div>

<h5 style="margin:8px 0 4px 0;">ACTA DE SESIÓN</h5>
<table style="width:100%; border-collapse:collapse; font-size:12px">
  <tr>
    <td style="width:60%"><b>Título:</b> ${o}</td>
    <td style="width:20%"><b>Tipo:</b> ${r}</td>
    <td style="width:20%"><b>Lugar:</b> ${a||"—"}</td>
  </tr>
</table>

<h5 style="margin:12px 0 4px 0;">1. Motivo de consulta</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">2. Desarrollo de la sesión</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">3. Observaciones</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">4. Recomendaciones / acuerdos</h5>
<ul><li>...</li></ul>

<div style="margin-top:38px;display:flex;justify-content:space-between;font-size:12px;">
  <div style="text-align:center;width:45%;">
    <div>__________________________</div>
    <div>Firma profesional</div>
  </div>
  <div style="text-align:center;width:45%;">
    <div>__________________________</div>
    <div>Vo.Bo.</div>
  </div>
</div>`},informe({casoId:e,fecha:t,titulo:o}){return`
<h4 style="margin:0 0 8px 0;">Informe psicológico</h4>
<div style="font-size:12px;color:#555;margin-bottom:8px">
  Caso ${e} — Fecha ${t}
</div>

<h5 style="margin:8px 0 4px 0;">Resumen</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">Antecedentes</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">Resultados / análisis</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">Conclusiones</h5>
<p>...</p>

<h5 style="margin:12px 0 4px 0;">Recomendaciones</h5>
<ul><li>...</li></ul>`},constancia({casoId:e,fecha:t,titulo:o}){return`
<div style="text-align:center; font-weight:700; text-decoration:underline; margin-bottom:12px;">CONSTANCIA DE ASISTENCIA</div>
<p>Se deja constancia que la persona atendió a la sesión psicológica indicada:</p>
<table style="width:100%; border-collapse:collapse; font-size:12px">
  <tr><td style="width:30%"><b>Cas o:</b></td><td>#${e}</td></tr>
  <tr><td><b>Título sesión:</b></td><td>${o}</td></tr>
  <tr><td><b>Fecha:</b></td><td>${t}</td></tr>
</table>

<p style="margin-top:28px">Se expide para fines que estime conveniente.</p>

<div style="margin-top:38px;text-align:center">
  __________________________<br>
  Firma profesional
</div>`},consentimiento({fecha:e,nombre:t,documento:o}){return`
<div style="text-align:center; font-weight:700; margin-bottom:12px;">
  CONSENTIMIENTO INFORMADO PSICOLOGÍA
</div>

<p style="font-size:12px; margin-bottom:8px;">
  <b>Fecha:</b> ${e||"______/______/______"}
</p>

<p style="font-size:12px; text-align:justify;">
  Sr.(a) Usuario, por favor lea atentamente el siguiente documento que tiene como objetivo explicarle el uso y la confidencialidad de sus datos, así como sus derechos, respecto al proceso de atención psicológica.
  Si tiene alguna duda y/o consulta lo puede realizar con el/la Psicólogo/a.
</p>

<p style="font-size:12px; margin-top:12px;">
  <b>Yo:</b> ${t||"_____________________________________________"}
</p>

<p style="font-size:12px; margin-top:8px;">
  <b>Con Documento Nro.:</b> ${o||"_____________________________"}
</p>

<p style="font-size:12px; text-align:justify; margin-top:12px;">
  Confirmo que he sido informado con claridad y veracidad, respecto al proceso de evaluaciones, sesiones, tiempos, las pruebas psicológicas que se me van a realizar, y también en cuanto a los resultados.
  De esta evaluación acepto, según los resultados de las pruebas psicológicas que se me realice, libre y voluntariamente, doy mi consentimiento para realizar las evaluaciones psicológicas.
</p>

<!--<div style="margin-top:48px; display:flex; justify-content:space-between; font-size:12px;">-->
<!--  <div style="text-align:center; width:30%;">-->
<!--    __________________________<br/>-->
<!--    FIRMA-->
<!--  </div>-->
<!--  <div style="text-align:center; width:30%;">-->
<!--    __________________________<br/>-->
<!--    NOMBRE-->
<!--  </div>-->
<!--  <div style="text-align:center; width:30%;">-->
<!--    __________________________<br/>-->
<!--    HUELLA DIGITAL-->
<!--  </div>-->
<!--</div>-->
<table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:48px;">
  <tr>
    <td style="width:33%; text-align:center;">
      __________________________<br/>
      FIRMA
    </td>
    <td style="width:33%; text-align:center;">
      __________________________<br/>
      NOMBRE
    </td>
    <td style="width:33%; text-align:center;">
    __________________________<br/>
      HUELLA DIGITAL
    </td>
  </tr>
</table>
`},informe_dio({numeroCaso:e,casoId:t,fecha:o,abogadoNombre:a,psicologoNombre:r,denunciante:n={},familiares:s=[],motivo:_="",antecedentes:w="",tecnicas:$="",conclusiones:c="",recomendaciones:q=""}){const d=(y,P="—")=>y==null||y===""?P:y;console.log(s);const h=(s&&s.length?s:[{nombre:"",edad:"",estado_civil:"",parentesco:"",ocupacion:""}]).map(y=>`
    <tr>
      <td>${d(y.familiar_nombre_completo)}</td>
      <td style="text-align:center">${d(y.familiar_edad)}</td>
      <td style="text-align:center">${d(y.familiar_estado_civil)}</td>
      <td style="text-align:center">${d(y.familiar_parentesco)}</td>
      <td style="text-align:center">${d(y.ocupacion)}</td>
    </tr>
  `).join("");return`
<div style="font-size:12px; line-height:1.35">
  <div style="text-align:center; font-weight:700; text-transform:uppercase;">
    INFORME PSICOLÓGICO
  </div>
  <div style="text-align:center; margin-top:2px; font-size:11px; color:#333">
    Nro. de Caso: ${d(e)}&nbsp;&nbsp;|&nbsp;&nbsp;Caso ID: #${d(t)}
  </div>

  <div style="margin-top:8px; border:1px solid #333; border-radius:6px; padding:10px;">
    <table style="width:100%; border-collapse:collapse; font-size:12px">
      <tr>
        <td style="width:10%;"><b>A:</b></td>
        <td> </td>
      </tr>
      <tr>
        <td></td>
        <td></td>
      </tr>
      <tr><td colspan="2" style="height:8px"></td></tr>
      <tr>
        <td><b>DE:</b></td>
        <td> ${d(r,"________________________________________")} </td>
      </tr>
      <tr>
        <td></td>
        <td>PSICÓLOGA / PSICÓLOGO – DIRECCIÓN DE IGUALDAD DE OPORTUNIDADES (DIO) SLIM</td>
      </tr>
      <tr>
        <td><b>Fecha:</b></td>
        <td>${d(o)}</td>
      </tr>
    </table>
  </div>

  <h5 style="margin:12px 0 4px 0;">1. DATOS PERSONALES DE LA DENUNCIANTE</h5>
  <table style="width:100%; border-collapse:collapse; font-size:12px">
    <tr>
      <td style="width:45%"><b>Nombres y apellidos:</b> ${d((n.nombres?n.nombres+" ":"")+(n.apellidos||""))}</td>
      <td style="width:20%"><b>Edad:</b> ${d(n.edad)}</td>
      <td style="width:35%"><b>Fecha de nacimiento:</b> ${d(n.fecha_nacimiento)}</td>
    </tr>
    <tr>
      <td><b>Lugar de nacimiento:</b> ${d(n.lugar_nacimiento)}</td>
      <td colspan="2"><b>Grado de instrucción:</b> ${d(n.grado)}</td>
    </tr>
    <tr>
      <td><b>Ocupación:</b> ${d(n.ocupacion)}</td>
      <td colspan="2"><b>Dirección/Domicilio:</b> ${d(n.domicilio)}</td>
    </tr>
    <tr>
      <td><b>Documento:</b> ${d(n.documento)}</td>
      <td><b>Nro.:</b> ${d(n.nro)}</td>
      <td><b>Teléfono:</b> ${d(n.telefono)}</td>
    </tr>
    <tr>
      <td colspan="3"><b>Estado civil:</b> ${d(n.estado_civil)}</td>
    </tr>
  </table>

  <h5 style="margin:12px 0 4px 0;">2. DATOS FAMILIARES</h5>
  <table style="width:100%; border:1px solid #333; border-collapse:collapse; font-size:12px">
    <thead>
      <tr style="background:#eee">
        <th style="border:1px solid #333; padding:4px; text-align:left;">Nombres y Apellidos</th>
        <th style="border:1px solid #333; padding:4px; width:60px;">Edad</th>
        <th style="border:1px solid #333; padding:4px; width:110px;">Estado civil</th>
        <th style="border:1px solid #333; padding:4px; width:110px;">Parentesco</th>
        <th style="border:1px solid #333; padding:4px; width:140px;">Ocupación</th>
      </tr>
    </thead>
    <tbody>
      ${h}
    </tbody>
  </table>

  <h5 style="margin:12px 0 4px 0;">3. MOTIVO DE CONSULTA</h5>


  <h5 style="margin:12px 0 4px 0;">4. ANTECEDENTES</h5>


  <h5 style="margin:12px 0 4px 0;">5. TÉCNICAS E INSTRUMENTOS EMPLEADOS</h5>


  <h5 style="margin:12px 0 4px 0;">6. CONCLUSIONES</h5>


  <h5 style="margin:12px 0 4px 0;">7. RECOMENDACIONES</h5>

  <p style="margin-top:12px">Se emite el presente informe en honor a la verdad para fines consiguientes.</p>

  <div style="margin-top:28px; display:flex; justify-content:space-between; font-size:12px;">
    <div style="text-align:center; width:45%;">
      __________________________<br/>
      ${d(r,"Psicóloga/o Interviniente")}
    </div>
    <div style="text-align:center; width:45%;">
      __________________________<br/>
      Vo.Bo.
    </div>
  </div>
</div>
`}},po=oe({name:"QBar",props:{...$e,dense:Boolean},setup(e,{slots:t}){const{proxy:{$q:o}}=te(),a=Ae(e,o),r=b(()=>`q-bar row no-wrap items-center q-bar--${e.dense===!0?"dense":"standard"}  q-bar--${a.value===!0?"dark":"light"}`);return()=>p("div",{class:r.value,role:"toolbar"},ne(t.default))}});export{co as Q,fo as S,po as a,lo as b,ro as c,uo as d,so as e};
