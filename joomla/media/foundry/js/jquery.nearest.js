Foundry.run(function(a){a.fn.nearest=function(b,c){var d=a(this),e,f;return a(b).each(function(){var b=a(this);if(d.get(0)==b.get(0))return;var g=a.distance(d,b,c);g>=0&&(f==undefined||g<f)&&(f=g,e=b)}),e};var b={left:[-1,0],up:[0,-1],right:[1,0],down:[0,1]};a.distance=function(a,c,d){function e(a,b){return Math.floor(Math.sqrt(a*a+b*b))}d=b[d];var f=a.offset(),g=f.left,h=f.top,i=g+a.outerWidth(),j=h+a.outerHeight(),k=g+a.outerWidth()/2,l=h+a.outerHeight()/2,m=c.offset(),n=m.left,o=m.top,p=n+c.outerWidth(),q=o+c.outerHeight(),r=n+c.outerWidth()/2,s=o+c.outerHeight()/2,t,u,v;return d[1]==0?(d[0]<0?(p<=g&&(u=g-p),r<=g&&(u!=undefined?u=Math.min(u,g-r):u=g-r),p<=g&&(u!=undefined?u=Math.min(u,g-p):u=g-p)):(i<=n&&(u=n-i),i<=r&&(u!=undefined?u=Math.min(u,r-i):u=r-i),g<n&&(u!=undefined?u=Math.min(u,n-g):u=n-g)),v=Math.min(Math.abs(l-o),Math.abs(l-s),Math.abs(l-q))*2):d[0]==0&&(d[1]<0?(q<=h&&(v=h-q),s<=h&&(v!=undefined?v=Math.min(v,h-s):v=h-s),q<=h&&(v!=undefined?v=Math.min(v,h-q):v=h-q)):(j<=o&&(v=o-j),j<=s&&(v!=undefined?v=Math.min(v,s-j):v=s-j),h<o&&(v!=undefined?v=Math.min(v,o-h):v=o-h)),u=Math.min(Math.abs(k-n),Math.abs(k-r),Math.abs(k-p))*2),u==undefined||v==undefined?t=-1:t=e(u,v),t}});