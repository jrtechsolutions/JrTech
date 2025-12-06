import Navbar from '@/components/home/Navbar';
import HeroSection from '@/components/home/HeroSection';
import FeaturesSection from '@/components/home/FeaturesSection';
import AboutSection from '@/components/home/AboutSection';
import ServicesSection from '@/components/home/ServicesSection';
import PortfolioSection from '@/components/home/PortfolioSection';
import ContactSection from '@/components/home/ContactSection';
import Footer from '@/components/home/Footer';
import FloatingWhatsApp from '@/components/home/FloatingWhatsApp';

export default function Index() {
  return (
    <div className="min-h-screen bg-slate-950 antialiased">
      <Navbar />
      <main>
        <HeroSection />
        <FeaturesSection />
        <AboutSection />
        <ServicesSection />
        <PortfolioSection />
        <ContactSection />
      </main>
      <Footer />
      <FloatingWhatsApp phoneNumber="5511949885625" />
    </div>
  );
}
