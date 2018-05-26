function startRainRedPacket() {
    document.getElementById("gameStage").style.zIndex = 100;
    var processDead = false;
    var totalTime = 30;
    var loadTimeDelay = 500;
    var countred = 2;
    var countyellow = 1;
    var scorered = 0;
    var scoreyellow = 0;
    var isClickOne = false;
    var game = new Phaser.Game(window.innerWidth, window.innerHeight, Phaser.CANVAS, 'gameStage');
    var states = {
        loading: function () {
            this.init = function () {
                if (!game.device.desktop) {
                    game.scale.scaleMode = Phaser.ScaleManager.EXACT_FIT;
                    game.scale.forcePortrait = true;
                } else {
                    game.scale.scaleMode = Phaser.ScaleManager.SHOW_ALL;
                }
                //游戏居中
                game.scale.pageAlignHorizontally = true;
                game.scale.pageAlignVertically = true;
                game.scale.refresh();
            };
            this.preload = function () {
                game.stage.backgroundColor = '#791265';
                game.load.image('process', assetConfig.process);
                game.load.image('processBg', assetConfig.processBg);
            };
            this.create = function () {
                console.log('loading : create');
                game.state.start('preload');
            };
        },
        preload: function () {
            //预加载阶段
            this.preload = function () {
                game.load.image('one', assetConfig.one);
                game.load.image('two', assetConfig.two);
                game.load.image('three', assetConfig.three);
                game.load.image('start', assetConfig.start);
                game.load.image('award', assetConfig.award);
                game.load.image('noaward', assetConfig.noaward);
                game.load.image('boom', assetConfig.boom);
                game.load.image('S', assetConfig.S);
                game.load.image('A', assetConfig.A);
                game.load.image('B', assetConfig.B);
                game.load.image('C', assetConfig.C);
                game.load.image('D', assetConfig.D);
                game.load.image('redpacket', assetConfig.redpacket);
                game.load.image('yellowpacket', assetConfig.yellowpacket);

                //loading menu
                var processBg = game.add.sprite(game.width / 2, game.height / 2-3, 'processBg');
                processBg.anchor.setTo(0.5, 0);
                processBg.width = 315;
                processBg.height = 21;

                var preloadSprite = game.add.sprite(game.width / 2 - 155, game.height / 2, 'process');
                preloadSprite.width = 315;
                preloadSprite.height = 16;
                preloadSprite.anchor.setTo(0, 0);
                preloadSprite.visible = false;
                var loadText = game.add.text(game.width / 2, game.height / 2 + 22, '0%', {
                    font: "bold 16px Arial",
                    fill: "#fff",
                    boundsAlignH: "center",
                    boundsAlignV: "middle"
                });
                loadText.anchor.setTo(0.5, 0);
                this.loadText = loadText;
                game.load.setPreloadSprite(preloadSprite);
                game.load.onLoadStart.add(function () {
                }, this);
                game.load.onFileComplete.add(function (progress, cacheKey, success, totalLoaded, totalFiles) {
                    loadText.text = progress + '%';
                }, this);
                game.load.onLoadComplete.add(function () {
                    processDead = true;

                }, this);

                console.log('preload : preload');
            };
            //该场景创建阶段
            this.create = function () {
                console.log('preload : create');
                var _this = this;
                var onLoad = function () {
                    if (processDead) {
                        _this.loadText.destroy();
                        game.state.start('create');
                    } else {
                        setTimeout(function () {
                            onLoad();
                        }, 1000);
                    }
                };
                setTimeout(function () {
                    onLoad();
                }, loadTimeDelay);
            }
        },
        create: function () {
            //该场景创建阶段
            this.create = function () {
                var start = game.add.sprite(0, 0, 'start');
                start.width = game.world.width;
                start.height = game.world.height;
                /*倒计时*/
                var spite1, spite2, spite3;
                var countdown = 2;
                spite3 = game.add.sprite(game.width / 2, game.height / 2, 'three');
                spite3.anchor.setTo(0.5, 0.5);
                var timeLoop = game.time.events.loop(1000, updateTime, this);

                function updateTime() {
                    switch (countdown) {
                        case 2 :
                            spite3.destroy();
                            spite2 = game.add.sprite(game.width / 2, game.height / 2, 'two');
                            spite2.anchor.setTo(0.5, 0.5);
                            countdown = 1;
                            break;
                        case 1 :
                            spite2.destroy();
                            spite1 = game.add.sprite(game.width / 2, game.height / 2, 'one');
                            spite1.anchor.setTo(0.5, 0.5);
                            countdown = 0;
                            break;
                        case 0 :
                            spite1.destroy();
                            game.time.events.remove(timeLoop);
                            game.state.start('play');
                            break;
                        default:
                            break;
                    }
                }
            }
        },
        play: function () {
            //该场景创建阶段
            this.create = function () {
                var start = game.add.sprite(0, 0, 'start');
                start.width = game.world.width;
                start.height = game.world.height;

                var titleGroup = game.add.group();
                var title = game.add.text(game.world.width / 2, 45, '游戏时间', {
                    fill: '#ffcc17',
                    fontSize: '33px',
                    fontWeight: 400
                }, titleGroup);
                title.anchor.setTo(0.5, 0.5);

                var timeSecond = game.add.text(game.world.width / 2, 85, '00:' + totalTime, {
                    fill: '#ffcc17',
                    fontSize: '33px',
                    fontWeight: 400
                }, titleGroup);
                timeSecond.anchor.setTo(0.5, 0.5);
                this.timeSecond = timeSecond;

                this.timeLoop = game.time.events.loop(1000, this.updateTime, this);

                var pockets = game.add.group();
                pockets.enableBody = true;
                pockets.setAll('outOfBoundsKill', true);
                pockets.setAll('checkWorldBounds', true);

                game.physics.startSystem(Phaser.Physics.Arcade);
                this.createTimer = game.time.events.loop(800, function () {
                    this.createPocket(pockets, 'redpacket', countred, this.addScorered);
                }, this);
                this.createTimer1 = game.time.events.loop(4000, function () {
                    this.createPocket(pockets, 'yellowpacket', countyellow, this.addScoreyellow);
                }, this);

                game.world.swap(titleGroup, pockets);

                //红包点击爆炸效果
                var score_explosions = game.add.group();
                this.score_explosions = score_explosions;
                score_explosions.createMultiple(30, 'boom');

                score_explosions.forEach(this.setupInvader, this);
            };
            this.updateTime = function () {
                if (totalTime >= 11) {
                    totalTime = totalTime - 1;
                    this.timeSecond.text = '00:' + totalTime;
                } else if (totalTime > 0 && totalTime < 11) {
                    totalTime = totalTime - 1;
                    this.timeSecond.text = '00:0' + totalTime;
                } else {
                    totalTime = 0;
                    game.time.events.remove(this.timeLoop);
                    game.state.start('over');
                }
            };
            this.createPocket = function (pockets, type, times, fun) {
                var img = game.cache.getImage(type);
                var w = game.world.width / 5;
                var h = w / img.width * img.height;

                for (var i = 0; i < times; i++) {
                    var x = game.rnd.integerInRange(0, 4 * w);
                    var y = -(h * (i + 1));
                    var pocket = pockets.create(game.rnd.integerInRange(0.5 * w, 4.5 * w), y, type);
                    pocket.width = w;
                    pocket.height = h;
                    pocket.anchor.setTo(0.5, 0.5);
                    pocket.angle = 360 * Math.random();
                    game.physics.enable(pocket);
                    pocket.body.gravity.y = game.rnd.integerInRange(100, 250);
                    pocket.inputEnabled = true;

                    pocket.events.onInputUp.add(fun, this);
                }
            };
            this.setupInvader = function (invader) {
                invader.anchor.x = 0.5;
                invader.anchor.y = 0.5;
                var b = game.cache.getImage('boom');
                invader.width = game.world.width / 5;
                invader.height = b.height * invader.width / b.width;
                invader.animations.add('kaboom');
            };
            this.addScorered = function (pocket) {
                this.scoreKabom(pocket);
                scorered += 1;
                pocket.destroy();
                this.getCoupon();
                this.setScoreAni();
            };
            this.addScoreyellow = function (pocket) {
                this.scoreKabom(pocket);
                scoreyellow += 1;
                pocket.destroy();
                this.getCoupon();
                this.setScoreAni();
            };
            this.getCoupon = function(){
                if(!isClickOne){
                    isClickOne = true;
                    vm.getCoupon();
                }
            };
            this.scoreKabom = function (alien) {
                var explosions = this.score_explosions;
                var explosion = explosions.getFirstExists(false);
                explosion.reset(alien.body.x + alien.width / 2, alien.body.y + alien.height / 2);
                explosion.play('kaboom', 4, false, true);
            };
            this.setScoreAni = function () {
                var ani = game.add.text(game.world.width / 2, game.world.height / 2, '+1', {
                    font: "26px Arial",
                    fill: "#ffe63c",
                    boundsAlignH: "center",
                    boundsAlignV: "middle",
                    fontWeight: 400
                });
                ani.anchor.setTo(0.5, 1);

                ani.alpha = 0;
                var showTween = game.add.tween(ani).to({
                    alpha: 1,
                    fontSize: '120px'
                }, 200, Phaser.Easing.Linear.None, true, 0, 0, false);

                showTween.onComplete.add(function () {
                    var hideTween = game.add.tween(ani).to({
                        alpha: 0,
                        fontSize: '26px'
                    }, 100, Phaser.Easing.Linear.None, true, 0, 0, false);
                    hideTween.onComplete.add(function () {
                        ani.destroy();
                    });
                });
            }
        },
        over: function () {
            //该场景创建阶段
            this.create = function () {
                var bg = game.add.image(0, 0, scorered > 0 && scoreyellow > 0 ? 'start' : 'start');
                bg.width = game.world.width;
                bg.height = game.world.height;

                if (scorered > 0 || scoreyellow > 0) {
                    this.setAward();
                } else {
                    this.setNone();
                }

                setTimeout(function () {
                    console.log('game over');
                }, 2000);
            };
            this.setAward = function () {
                var over = game.add.image(game.world.width / 2, game.world.height / 2 - 75, 'award');
                over.scale.setTo(0.5);
                over.anchor.setTo(0.5);
                var text2 = game.add.text(game.world.width / 2-25, game.world.height/2-140, scoreyellow, {
                    font: "40px PingFang",
                    fill: "#fff",
                    setShadow:"0 0 8px rgba(255,224,55,.18)",
                    stroke:"#ffcc17",
                    strokeThickness:"1",
                    boundsAlignH: "center",
                    boundsAlignV: "middle",
                    fontWeight: 400
                });
                text2.anchor.setTo(0.5);

                var text3 = game.add.text(game.world.width / 2+75, game.world.height/2-140, scorered, {
                    font: "40px PingFang",
                    fill: "#fff",
                    setShadow:"0 0 8px rgba(255,224,55,.18)",
                    stroke:"#ffcc17",
                    strokeThickness:"1",
                    boundsAlignH: "center",
                    boundsAlignV: "middle",
                    fontWeight: 400
                });
                text3.anchor.setTo(0.5);

                var level = this.getAwardLevel(scoreyellow,scorered);

                var text4 = game.add.text(game.world.width / 2+65, game.world.height/2-82, level, {
                    font: "65px PingFang",
                    fill: "#fff",
                    border:"solid 1px #ffcc17",
                    setShadow:"0 0 8px rgba(255,224,55,.18)",
                    stroke:"#ffcc17",
                    strokeThickness:"2",
                    boundsAlignH: "center",
                    boundsAlignV: "middle",
                    fontWeight: 400
                });
                text4.anchor.setTo(0.5);
                var coupon = game.add.image(game.world.width / 2, game.world.height / 2 + 10, couponLevel);
                coupon.scale.setTo(0.5);
                coupon.anchor.setTo(0.5);

                var text = game.add.text(game.world.width / 2, game.world.height / 2 + 118, '收下奖品', {
                    font: "38px Arial",
                    fill: "#f44326",
                    boundsAlignH: "center",
                    boundsAlignV: "middle",
                    fontWeight: 400
                });
                text.scale.setTo(0.5);
                text.anchor.setTo(0.5);
                text.inputEnabled = true;
                vm.scrollToTop();
                text.events.onInputUp.add(function () {
                    document.getElementById("gameStage").innerHTML = '';
                    document.getElementById("gameStage").style.zIndex = -1;
                    game.destroy();
                }, this);

                text2.addColor('#fee433', 4);
                text2.addColor('#fff', 4 + (scorered + "").length);
                text2.anchor.setTo(0.5);
            };
            this.setNone = function () {
                var over = game.add.image(game.world.width / 2, game.world.height / 2 - 75, 'noaward');
                over.scale.setTo(0.5);
                over.anchor.setTo(0.5);

                var text = game.add.text(game.world.width / 2, game.world.height / 2 + 118, '我知道了', {
                    font: "38px Arial",
                    fill: "#f44326",
                    boundsAlignH: "center",
                    boundsAlignV: "middle",
                    fontWeight: 400
                });
                text.scale.setTo(0.5);
                text.anchor.setTo(0.5);
                text.inputEnabled = true;
                vm.scrollToTop();
                text.events.onInputUp.add(function () {
                    document.getElementById("gameStage").innerHTML = '';
                    document.getElementById("gameStage").style.zIndex = -1;
                    game.destroy();
                }, this);
            };
            this.getAwardLevel = function (scoreyellow,scorered) {
                var score = scoreyellow*5 + scorered;
                if(score>60){
                    return "S";
                } else if(score>41&&score<=60){
                    return "A";
                } else if(score>21&&score<=40){
                    return "B";
                } else if(score>=1&&score<=20){
                    return "C";
                }
            }
        }
    };
    game.state.add('loading', states.loading);
    game.state.add('preload', states.preload);
    game.state.add('create', states.create);
    game.state.add('play', states.play);
    game.state.add('over', states.over);
    //开始游戏场景
    game.state.start('loading');
}