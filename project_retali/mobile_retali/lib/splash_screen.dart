import 'dart:async';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/login_screen.dart';
import 'screens/home_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with TickerProviderStateMixin {
  late AnimationController _logoController;
  late Animation<double> _logoScale;

  final String _fullText = "Retali Mustajab Travel";
  String _visibleText = "";
  bool _startTyping = false;
  bool _showCursor = true;

  @override
  void initState() {
    super.initState();

    // 1. Animasi Logo (1.5 detik agar lebih dinamis)
    _logoController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    );

    _logoScale = Tween<double>(begin: 0.5, end: 1.0).animate(
      CurvedAnimation(
        parent: _logoController,
        curve: Curves.easeOutBack, 
      ),
    );

    _logoController.forward();

    // 2. Mulai mengetik teks saat animasi logo hampir selesai
    Future.delayed(const Duration(milliseconds: 800), () {
      if (mounted) {
        setState(() => _startTyping = true);
        _startTypingEffect();
        _startCursorBlink();
      }
    });

    // 3. Cek Login & Navigasi (Total tunggu 4 detik)
    _checkLogin();
  }

  void _startTypingEffect() {
    int index = 0;
    Timer.periodic(const Duration(milliseconds: 70), (timer) {
      if (index < _fullText.length) {
        if (mounted) {
          setState(() {
            _visibleText += _fullText[index];
            index++;
          });
        }
      } else {
        timer.cancel();
      }
    });
  }

  void _startCursorBlink() {
    Timer.periodic(const Duration(milliseconds: 400), (timer) {
      if (!mounted) {
        timer.cancel();
        return;
      }
      setState(() {
        _showCursor = !_showCursor;
      });
    });
  }

  Future<void> _checkLogin() async {
    // Memberikan waktu user melihat animasi tanpa menunggu terlalu lama
    await Future.delayed(const Duration(milliseconds: 4000));

    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    if (!mounted) return;

    if (token != null && token.isNotEmpty) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const HomeScreen()),
      );
    } else {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const LoginScreen()),
      );
    }
  }

  @override
  void dispose() {
    _logoController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // LOGO DENGAN ANIMASI ZOOM
            ScaleTransition(
              scale: _logoScale,
              child: Image.asset(
                'assets/LogoRetali.png',
                width: 180,
              ),
            ),

            const SizedBox(height: 40),

            // TEKS DENGAN EFEK MENGETIK
            if (_startTyping)
              SizedBox(
                height: 30, // Mencegah layout bergeser saat teks muncul
                child: RichText(
                  text: TextSpan(
                    children: [
                      TextSpan(
                        text: _visibleText,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF842D62),
                        ),
                      ),
                      TextSpan(
                        text: _showCursor ? "|" : " ",
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF842D62),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}