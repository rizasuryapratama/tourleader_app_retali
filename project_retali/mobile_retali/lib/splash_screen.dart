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

class _SplashScreenState extends State<SplashScreen>
    with TickerProviderStateMixin {

  late AnimationController _logoController;
  late Animation<double> _logoScale;

  String _fullText = "Retali Mustajab Travel";
  String _visibleText = "";
  bool _startTyping = false;
  bool _showCursor = true;

  @override
  void initState() {
    super.initState();

    // 🔥 LOGO ZOOM LEBIH LAMA & HALUS
    _logoController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 3),
    );

    _logoScale = Tween<double>(begin: 0.2, end: 1.0).animate(
      CurvedAnimation(
        parent: _logoController,
        curve: Curves.easeOutBack,
      ),
    );

    _logoController.forward();

    // ⏳ Setelah logo selesai → mulai typing
    Future.delayed(const Duration(seconds: 3), () {
      setState(() => _startTyping = true);
      _startTypingEffect();
      _startCursorBlink();
    });

    // 🔐 AUTO LOGIN CHECK (lebih lama biar animasi keliatan)
    _checkLogin();
  }

  void _startTypingEffect() {
    int index = 0;

    Timer.periodic(const Duration(milliseconds: 120), (timer) {
      if (index < _fullText.length) {
        setState(() {
          _visibleText += _fullText[index];
          index++;
        });
      } else {
        timer.cancel();
      }
    });
  }

  void _startCursorBlink() {
    Timer.periodic(const Duration(milliseconds: 500), (timer) {
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
    await Future.delayed(const Duration(seconds: 6));

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

            // 🔥 LOGO ZOOM
            ScaleTransition(
              scale: _logoScale,
              child: Image.asset(
                'assets/LogoRetali.png',
                width: 180,
              ),
            ),

            const SizedBox(height: 50),

            // 🔥 TYPING TEXT + CURSOR
            if (_startTyping)
              RichText(
                text: TextSpan(
                  children: [
                    TextSpan(
                      text: _visibleText,
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF842D62),
                      ),
                    ),
                    TextSpan(
                      text: _showCursor ? "|" : " ",
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF842D62),
                      ),
                    ),
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }
}
