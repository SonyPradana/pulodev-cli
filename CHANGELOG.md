# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Menambahkan paket versi stabil di guthub [ver 0.2](https://github.com/SonyPradana/pulodev-cli/releases/tag/0.2)
### Fix
- Command --Update -global dost work because parameter $args not found

## [0.2.0] - 2021-06-11
### Added
- Support ./vendor/bin/pulo composer
- Support global install via composer
- Support pagination view #1
- Support self update using composer

### Fix
- Dukungan apabila curl extention tidak tersedia menggunakan file_get_contents()
- Error saat satu argument commandline tidak terformat dengan baik (pagination)

### Changed
- Merubah cli option dari 'kontent' menjadi 'konten'
- Splin code echo class Main mendaji perfungsi command
- Merubah parameter di class PuloDev mendadi parameter option, karena arugment terlalu panjang

## [0.1.0] - 2021-06-06
### Added
- Pulo.dev in cli write using php
- This poject start by [Angger Pradana](https://github.com/SonyPradana)
- Open Source Project

