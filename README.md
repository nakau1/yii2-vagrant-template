# wasabi ![](https://circleci.com/gh/oz-sysb/wasabi.svg?style=shield&circle-token=35bdb750bd4362e274db3e93340d5387d65530bb)

# 環境構築手順

ローカル環境の構築手順です

## 前準備

Vagrantに必要なツールをインストールしてください

- Virtual Box https://www.virtualbox.org/
- Vagrant http://www.vagrantup.com/

## セットアップ

必要なプラグインのインストール

```sh
vagrant plugin install vagrant-omnibus
vagrant plugin install vagrant-vbguest
```

hostsの編集

```
172.16.22.10    pollet.vagrant.net
```

vagrant の開始
### Windowsの場合
非推奨環境
今後更新があった際の動作保証はできないが手順は[こちら](https://github.com/oz-sysb/wasabi/wiki/Windowsでの環境構築)

### Mac や ubuntu 等の Unix 系OSの場合

プロジェクトルートにある Vagrantfile.unix のファイルを Vagrantfile としてコピーして保存してください

その後 vagrant を起動します

```sh
vagrant up
```

ansible のレシピは自動で実行されます

#### 2回目以降

次からは up するだけで vm 環境が立ち上がります

```sh
vagrant up
```

レシピの変更があった際には provision で立ち上げてください

すでに vagrant が立ち上がっている場合

```sh
vagrant provision
```

まだ vagrant が立ち上がっていない場合

```sh
vagrant up -provision
```

## migration について

DBの変更はmigrationで管理しています。

変更があった場合は

```
vagrant ssh
```

```
cd /var/www/wasabi/src
php yii migrate/up
```

を実行してください

## ログ設計
### ログレベルの種類  
 [Yiiで用意されているもの](https://github.com/yiisoft/yii2/blob/master/docs/guide-ja/runtime-logging.md#メッセージを記録する-)を踏襲
 
|レベル|概要|説明|出力先|運用時の対応|
|:--|:--|:--|:--|:--|
|error|エラー|予期しないその他の実行時エラー|ファイル, コンソール, メール|営業時間で対応|
|warning|警告|実行時に生じた異常とは言い切れないが正常とも異なる何らかの予期しない問題|ファイル, コンソール|該当箇所の変更リリースがあるときに修正|
|info|情報|実行時の何らかの注目すべき事象（開始や終了など）|ファイル, コンソール|対応しない|
|trace|トレース情報|システムの動作状況に関する詳細な情報|ファイル|対応しない|

### ログ出力・収集に使うツール
 - 収集  
未検討
ログが集まってやりたいことが増えたフェーズで検討したい。  
 - 出力  
 Yiiの設定でサーバーごとにファイル出力
 日次バッチ等でひとつの場所にまとめる
 
### ログの出力場所
`runtime`ディレクトリ配下  
本番環境のデプロイにElastic Beans Talkを使用するのでデプロイ時にruntime配下のファイルは初期化される  
本番運用開始前にログの転送設定や退避方法を検討する  

### ログの保存期間、ローテーション間隔
 細かくは追々決めたいが週単位や日単位 
 ファイル容量次第

### フォーマット
[Yiiで用意されているもの](https://github.com/yiisoft/yii2/blob/master/docs/guide-ja/runtime-logging.md#メッセージの書式設定)を踏襲

```
タイムスタンプ [IP アドレス][ユーザ ID][セッション ID][重要性レベル][カテゴリ] メッセージテキスト
```	

#### メッセージテキスト
メッセージテキスト部分には実行元のクラス名/メソッド名を実行元名として記載する  
実行元名はControllerで[getRoute()](http://www.yiiframework.com/doc-2.0/yii-base-controller.html#getRoute()-detail)を使う  

e.x. `yii make-cedyna-payment-file/index`で動かせるバッチの場合以下のような出力になる  
```bash
[vagrant@localhost logs]$ pwd
/vagrant/runtime/logs
[vagrant@localhost logs]$ tail -100  app.log | grep application | grep 'make-cedyna-payment-file/index'
2016-09-08 04:05:14 [info][application] begin batch: make-cedyna-payment-file/index
2016-09-08 04:05:14 [info][application] begin processing make payment file: make-cedyna-payment-file/index
2016-09-08 04:05:14 [info][application] data does not exist : make-cedyna-payment-file/index
2016-09-08 04:05:14 [info][application] finish batch: make-cedyna-payment-file/index
```


### 共通の出力内容  
 - errorのときはコールスタックをつける  
参考：[メッセージのトレースレベル](https://github.com/yiisoft/yii2/blob/master/docs/guide-ja/runtime-logging.md#メッセージのトレースレベル-)
